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
use Illuminate\Support\Facades\DB;

class CouponController extends Controller implements CrudInterface_FH
{
    protected $paymentService;
    private $isPaginate = false;
    private $defaultPageSize = 10;
    private $defaultPageNum = 1;

    // Injected Service 
    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

// -- to fetch all coupons with pagination --
    public function getCoupons(CouponRequest $request) {
        try {
            // check if there are any request params to filter coupons
            if ($request->query()) {
                // calling function to filter products based on request params 
                $filteredCoupon = $this->filterCoupon($request);
                // check for pagination and get coupons
                $coupons = $this->checkPaginationAndGetCoupons($request, $filteredCoupon);
            }
            // else fetch all products
            else {
                // check for pagination
                $coupons = $this->checkPaginationAndGetCoupons($request);
            }

            return successResponse('Coupons fetched successfully!', CouponResource::collection($coupons), $this->isPaginate, 200);
        } catch (\Exception $e) {
            return handleException($e);
        }
    }

    public function checkPaginationAndGetCoupons($request, $filteredCoupon = null)
    {
        try {
            $pageSize = $request->input('pageSize', $this->defaultPageSize);
            $pageNum = $request->input('pageNum', $this->defaultPageNum);
    
            if ($request->input('paginate')) {
                $this->isPaginate = true;
                if ($filteredCoupon) {
                    return $filteredCoupon->paginate($pageSize, ['*'], 'page', $pageNum);
                } else {
                    return Coupon::paginate($pageSize, ['*'], 'page', $pageNum);
                }
            } else {
                $this->isPaginate = false;
                if ($filteredCoupon) {
                    return $filteredCoupon->get();
                } else {
                    return Coupon::all();                
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    // Filtering coupons with respect to request params
    public function filterCoupon($request)
    {
        return Coupon::when($request->filled('expiry_before') && $request->filled('expiry_after'), function ($query) use ($request) {
                return $query->whereBetween('expiry', [$request->expiry_before, $request->expiry_after]);
            })
            ->when($request->filled('expiry_after'), function ($query) use ($request) {
                return $query->where('expiry', '>=', $request->expiry_after);
            })
            ->when($request->filled('expiry_before'), function ($query) use ($request) {
                return $query->where('expiry', '<=', $request->expiry_before);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->input('product_id') === 'null', function ($query) {
                return $query->whereNull('product_id');
            })
            ->when($request->filled('product_id') && $request->input('product_id') !== 'null', function ($query) use ($request) {
                return $query->where('product_id', $request->product_id);
            })
            ->when($request->filled('stripe_price_id'), function ($query) use ($request) {
                return $query->where('stripe_price_id', $request->stripe_price_id);
            })
            ->when($request->filled('discount'), function ($query) use ($request) {
                return $query->where('discount', $request->discount);
            })
            ->when($request->filled('discount_type'), function ($query) use ($request) {
                return $query->where('discount_type', $request->discount_type);
            })
            ->when($request->filled('code'), function ($query) use ($request) {
                return $query->whereHas('couponCodes', function($query) use ($request) {
                    return $query->where('code', $request->code);
                });
            });
    }

// -- interface function --
    public function index(){
        // try {
        //    return Coupon::paginate($this->defaultPageSize);
        // } catch (\Exception $e) {
        //     return handleException($e);
        // }
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
        DB::beginTransaction();
        try {
            // Check if coupon is for product or cart
            $couponPayload = $this->isProductCoupon($payload);

            // Create the coupon
            $coupon = Coupon::create($couponPayload);

            // Create Coupon Codes
            if($coupon) {
                $payload['coupon_id'] = $coupon->id;
            }
    
            if (array_key_exists("code_count" ,$payload) && $payload['code_count'] > 1) {
               $this->generateMultipleCouponCodes($payload);
            } else {
               $this->generateSingleCouponCode($payload);
            }

            DB::commit();
            return successResponse("Coupon created successfully!", CouponResource::make($coupon));

        } catch (\Exception $e) {
            DB::rollBack();
            return handleException($e);
        }

     }
 
// -- Update coupon --

     public function edit(CouponRequest $request, $id)
     {
        $validatedData = $request->validated();
        return $this->update($validatedData, $id);
     }

     public function update(array $payload, $id) {
        DB::beginTransaction();
    
        try {
            // check if payload has product id
            if(array_key_exists("product_id",$payload)){
                // Check if coupon is for product or cart
                $payload = $this->isProductCoupon($payload);
            }

            // Find the coupon
            $coupon = Coupon::findOrFail($id);
            
            // Update coupon details
            $coupon->update($payload);
    
            // Update coupon codes if provided
            if (isset($payload['coupon_codes'])) {
                foreach ($payload['coupon_codes'] as $codeData) {
                    // Find the coupon code
                    $couponCode = CouponCode::findOrFail($codeData['id']);
                    
                    // Update coupon code details
                    $couponCode->update($codeData);
                }
            }
            
            DB::commit();
    
            return successResponse("Coupon and coupon codes updated successfully!", CouponResource::make($coupon));
    
        } catch (\Exception $e) {
            DB::rollBack();
            return handleException($e);
        }
    
     }
     
// -- Delete coupon --
     public function destroy($id) {
        try {
            $coupon = Coupon::findorfail($id);
            $coupon->delete();
            return successResponse("Coupon deleted Successfully!");
        } catch (\Exception $e) {
            return handleException($e);
        }
     }

// -- check if coupon is for product --
     public function isProductCoupon($payload) {
        try {
            if (array_key_exists("product_id" ,$payload) && !is_null($payload['product_id'])) {
                  //get product 
                  $product = Product::findorfail($payload["product_id"]);

                  // calculate discounted price
                  $discountedPrice = $this->calculateDiscountedPrice($product->price, $payload);
               
                  // check discounted price on stripe
                  $pricePayload = $this->generatePricePayload($discountedPrice, $product);
                  $priceId = $this->getStripePriceId($pricePayload); 
                  $payload['stripe_price_id'] = $priceId;
              } 

              return $payload;
        }         
        catch (\Exception $e) {
            throw $e;
        }
     }

// -- check the discount type and call respective function to calculate the discounted price --
     public function calculateDiscountedPrice($price, $payload) {
        if($payload['discount_type'] == 'percent') {
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
            // convert discount price from dollar into cents
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
