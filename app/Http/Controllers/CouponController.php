<?php

namespace App\Http\Controllers;
use App\Http\Interfaces\CrudInterface_FH;
use App\Http\Interfaces\PaymentServiceInterface;
use App\Http\Requests\CouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Models\CouponCode;
use App\Models\Product;
use Illuminate\Support\Str;

class CouponController extends Controller implements CrudInterface_FH
{
    protected $paymentService;
    private $defaultPageSize = 10;

    // Injected Service 
    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }
// -- to fetch all coupons with pagination --
    public function index() {
        try {
           $coupons = Coupon::paginate($this->defaultPageSize);
           return successResponse("Coupon fetched successfully!", CouponResource::collection($coupons),true, 200);
        } catch (\Exception $e) {
            return handleException($e);
        }
    }

// -- get a specific coupon by id --
     public function show($id) {
        try {
            $coupon = Coupon::with("couponCodes")->findOrFail($id);
            return successResponse("Coupon fetched successfully!", CouponResource::make($coupon));
        }
        catch (\Exception $e) {
            return handleException($e);
        }
     }

// -- Create a coupon with coupon codes --
     public function create(CouponRequest $request)
     {
         $validatedData = $request->validated();
         return $this->store($validatedData);
     }

 
     public function store(array $payload) {
        try {
            // Check if coupon is for product or cart
            if (array_key_exists("product_id" ,$payload) && !is_null($payload['product_id'])) {
              // ----- Coupon is for product -----
                //get product price
                $product = Product::find($payload["product_id"]);
                // calculate discounted price
                $discountedPrice = $this->calculateDiscountedPrice($product->price, $payload);
             
                // check discounted price on stripe
                $pricePayload = $this->generatePricePayload($discountedPrice, $product);
                $priceId = $this->getStripePriceId($pricePayload); 
                $payload['stripe_price_id'] = $priceId;
            } else {
              // ----- Coupon is for cart -----
                $payload["stripe_price_id"] = null;
            }

            // Create the coupon
            $coupon = Coupon::create($payload);

            // Create Coupon Codes
            if($coupon) {
                $payload['coupon_id'] = $coupon->id;
            }
    
            if (array_key_exists("code_count" ,$payload) && $payload['code_count'] > 1) {
               $this->generateMultipleCouponCodes($payload);
            } else {
               $this->generateSingleCouponCode($payload);
            }

            return successResponse("Coupon Created successfully!", CouponResource::make($coupon));

        } catch (\Exception $e) {
            return handleException($e);
        }

     }
 
// -- Update coupon --
     public function update(array $payload, $id) {

     }
     
// -- Delete coupon --
     public function destroy($id) {

     }

// -- check the discount type and call respective function to calculate the discounted price --
     public function calculateDiscountedPrice($price, $payload) {
        if($payload['discount_type'] == 'percentage') {
            return $this->percentageDiscount($price, $payload['discount']);
        } 
        else {
            return $this->flatDiscount($price, $payload['discount']);
        } 
     }

// -- calculate price when discount is in % --
     public function percentageDiscount($price, $discount) {
        // Convert percentage to decimal
        $discountDecimal = $discount / 100;

        // Calculate discount amount
        $discountAmount = $price * $discountDecimal;

        // Calculate new price after discount
        $discountedPriceInDollars = $price - $discountAmount;

        // Convert new price to cents
        return round($discountedPriceInDollars,2);
     }

// -- calculate price when discount is flat --
     public function flatDiscount($price, $discount) {
        // Calculate new price after discount
        $discountedPriceInDollars = $price - $discount;

        // Convert new price 
        return round($discountedPriceInDollars,2);
     }

// -- generate payload for getting the stripe price id --
     public function generatePricePayload($discountedPrice, $product){
        return [
            'price' => $discountedPrice,
            'stripe_product_id' => $product->stripe_product_id
        ];
     }

// -- get the product's stripe price id --
     public function getStripePriceId($pricePayload) {
        try {
            $discountedPrice= $pricePayload['price'] * 100;

            // Fetch all prices for the specific product
            $prices = $this->paymentService->getPriceByProductId($pricePayload['stripe_product_id']);
         
            // Filter prices to find one that matches the discounted price
            $matchingPrice = array_filter($prices->data, function ($price) use ($discountedPrice) {
                return isset($price->unit_amount) && $price->unit_amount == $discountedPrice;
            });
            // Return the Price ID if a matching price is found
            if (!empty($matchingPrice)) {
                // Only return the first matching price ID
                return array_values($matchingPrice)[0]->id;
            }
            else {
               // price doesn't exists on stripe so create it 
               $stripePrice = $this->paymentService->createPrice($pricePayload);
               return $stripePrice->id;
            }
    
        }
        catch (\Exception $e) {
            throw $e;
        }
     }

// -- generate multiple coupon codes --
     private function generateMultipleCouponCodes($payload)
     {
        try {
            $codes = [];
            $couponName = $this->sanitizeCode($payload["name"]);
            for ($i = 0; $i < $payload["code_count"]; $i++) {
                $uniqueCode = $couponName . '-' . Str::upper(Str::random(4)); 
                $codes[] = [
                    'coupon_id' => $payload["coupon_id"],
                    'code' => $uniqueCode,
                    'usage_limit' => $payload["usage_limit"],
                    'usage_per_user' => $payload["usage_per_user"],
                ];
            }
    
            return CouponCode::insert($codes);
        } 
        catch (\Exception $e) {
            throw $e;
        }
     }

// -- generate single coupon code --
     private function generateSingleCouponCode($payload)
    {
        try {
            $couponName = $this->sanitizeCode($payload['name']);
            $uniqueCode = $couponName . '-' . Str::upper(Str::random(4)); 

            return CouponCode::create([
                'coupon_id' => $payload["coupon_id"],
                'code' => $uniqueCode,
                'usage_limit' => $payload["usage_limit"],
                'usage_per_user' => $payload["usage_per_user"],
            ]);
        } 
        catch (\Exception $e) {
            throw $e;
        }
    }

// -- sanitizing coupon name to create the coupon code --
     private function sanitizeCode($couponName)
     {
         // Remove special characters, replace spaces with hyphens, and convert to uppercase
         return strtoupper(preg_replace('/[^A-Za-z0-9]+/', '-', $couponName));
     }
 
}
