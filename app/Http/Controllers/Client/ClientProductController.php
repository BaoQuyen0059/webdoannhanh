<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Libraries\BaseHttpResponse;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class ClientProductController extends Controller
{
    protected $viewPath = 'client.products';

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Review
     */
    protected $review;

    /**
     * ClientProductController constructor.
     * @param Category $category
     * @param Product $product
     * @param Review $review
     */
    public function __construct(Category $category, Product $product, Review $review)
    {
        $this->category = $category;
        $this->product = $product;
        $this->review = $review;
    }

    /**
     * List product
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $categories = $this->category->whereActive(1)
            ->orderBy('id', 'asc')
            ->select(['id', 'slug', 'name', 'active', 'parent_id'])
            ->get();

        $products = $this->product->whereActive('published')
            ->orderBy('id', 'desc')
            ->select(['id', 'name', 'slug', 'thumb', 'price', 'description'])
            ->paginate(12);

        return view("{$this->viewPath}.index")->with([
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Product by Category
     *
     * @param string $slug
     * @return Application|Factory|View
     */
    public function category($slug = "")
    {
        if (!$slug || !$result = $this->category->whereSlug($slug)->whereActive(1)->first()) {
            abort(404);
        } else {
            $categories = $this->category->whereActive(1)
                ->orderBy('id', 'asc')
                ->select(['id', 'slug', 'name', 'active', 'parent_id'])
                ->get();

            $products = $this->product->whereCategoryId($result->id)
                ->whereActive('published')
                ->orderBy('id', 'desc')
                ->select(['id', 'name', 'slug', 'thumb', 'price', 'description'])
                ->paginate(12);

            return view("{$this->viewPath}.category")->with([
                'result' => $result,
                'products' => $products,
                'categories' => $categories,
            ]);
        }
    }

    /**
     * Product detail
     *
     * @param string $slug
     * @return Application|Factory|View
     */
    public function product($slug = "")
    {
        if (!$slug || !$result = $this->product->whereSlug($slug)->whereActive('published')->first()) {
            abort(404);
        } else {
            $existsReview = false;

            if ($customer_id = auth()->id()) {
                $exists = $this->review->whereCustomerId($customer_id)->whereProductId($result['id'])->get()->count();
                if ($exists > 0) {
                    $existsReview = true;
                }
            }

            $productRelates = $this->product->whereNotIn('id', [$result['id']])
                ->whereActive('published')
                ->whereCategoryId($result['category_id'])
                ->orderBy('id', 'desc')
                ->select(['id', 'name', 'slug', 'thumb', 'price', 'description'])
                ->take(6)->get();

            $result->views += 1;
            $result->save();


            return view("{$this->viewPath}.show")->with([
                'result' => $result,
                'existsReview' => $existsReview,
                'productRelates' => $productRelates
            ]);
        }
    }

    /**
     * Create reviews
     *
     * @param ReviewRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function review(ReviewRequest $request, BaseHttpResponse $response)
    {
        $customer_id = auth()->id();

        if (!$customer_id) {
            return $response->setError()->setMessage("Vui lòng đăng nhập trước!");
        } else {
            $product_id = $request['product_id'];
            $exists = $this->review->whereCustomerId($customer_id)->whereProductId($product_id)->get()->count();

            if ($exists > 0) {
                return $response->setError()->setMessage("Bạn đã đánh giá sản phẩm này rồi!");
            }

            $this->review->create([
                'product_id' => $product_id,
                'customer_id' => $customer_id,
                'point' => $request['star'],
                'comment' => $request['comment'],
            ]);

            return $response->setMessage("Đánh giá sản phẩm thành công!");
        }
    }
}
