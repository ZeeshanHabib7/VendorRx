<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Interfaces\CrudInterface_FH;
use App\Models\Product;
use Exception;
use Stripe\Stripe;
use App\Http\Interfaces\PaymentServiceInterface;

class ProductController extends Controller implements CrudInterface_FH
{
    private $isPaginate = false;
    private $defaultPageSize = 10;
    private $defaultPageNum = 1;
    protected $paymentService;

    // Injected Service 
    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    // Fetch products
    public function getProducts(ProductRequest $request)
    {
        try {
            // check if there are any request params to filter products
            if ($request->query()) {
                // calling function to filter products based on request params 
                $filteredProducts = $this->filterProduct($request);
                
                // check for pagination
                if ($request->input('paginate')){
                    $pageSize = $request->input('pageSize', $this->defaultPageSize);
                    $pageNum = $request->input('pageNum', $this->defaultPageNum);
                    $this->isPaginate = true;
                    $products = $filteredProducts->paginate($pageSize, ['*'], 'page', $pageNum);
                } 
                // else fetch all products without pagination
                else {
                    $products = $filteredProducts->get();
                }
            }
            // else fetch all products
            else {
                 $products = $this->index();
            }
        
            return successResponse( 'Data fetched successfully!',ProductCollection::collection($products),$this->isPaginate, 200);
        }
        catch (Exception $e) {
            return handleException($e);
        }   
    }

    // Filtering products with respect to request params
    public function filterProduct($request) {
       return Product::when($request->filled('startDate') && $request->filled('endDate'), function ($query) use ($request) {
            return $query->whereBetween('date', [$request->startDate, $request->endDate]);
        })
        ->when($request->start_date, function ($query, $startDate) {
            return $query->where('date', '>=', $startDate);
        })
        ->when($request->end_date, function ($query, $endDate) {
            return $query->where('date', '<=', $endDate);
        })
        ->when($request->filled('brand'), function ($query) use ($request) {
            return $query->where('brand', $request->brand);
        })
        ->when($request->filled('minPrice') && $request->filled('maxPrice'), function ($query) use ($request) {
            return $query->whereBetween('price', [$request->minPrice, $request->maxPrice]);
        })
        ->when($request->filled('keyword'), function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->keyword . '%');
        });   
    }

    // get all products
    public function index()
    {
        try {
            // get all products
            return Product::all();
        } 
        catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }

    // Add product
    public function create(ProductRequest $request) 
    {
        $validatedData = $request->validated();
        return $this->store($validatedData);
    }

    public function store(array $payload)
    { 
        try {
            // Stripe Key
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            // Create Product on stripe
            $stripeProduct = $this->paymentService->createProduct($payload);
            // added stripe product id in payload
            $payload["stripe_product_id"] = $stripeProduct->id;
            // Create Price on stripe
            $stripePrice = $this->paymentService->createPrice($payload);
            $payload['stripe_price_id'] = $stripePrice->id;
            
            // create product
            $product = Product::create($payload);

            // success reponse upon creation
            return successResponse("Product Added Successfully!", ProductCollection::make($product));
        } 
        catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            // return handleException($e);
            return $e;
        }
    }

    public function edit(ProductRequest $request, $id)
    {
        $validatedData = $request->validated();
        return $this->update($validatedData,$id);
    }


    public function update(array $payload, $id)
    {
        try {
            $product = Product::findOrFail($id);
            // update product
            $product->update($payload);  
            // get updated product
            $updatedproduct = Product::find($product->id);  
            // success response upon updation
            return successResponse("Product Updated Successfully!",ProductCollection::make($updatedproduct));
        } 
        catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }    
        
    }

    public function destroy($id)
    {
        try {
            // delete product
            $product = Product::find($id);
            $product->delete();
            // success response upon deletion
            return successResponse("Product Deleted Successfully!");
        } catch (\Exception $e) {
            // Handle any exceptions that may occur during the process
            return handleException($e);
        }
    }


}
