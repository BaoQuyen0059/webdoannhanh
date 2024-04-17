<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\News;
use App\Models\Newsletter;
use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * @var Newsletter
     */
    protected $newsletter;

    /**
     * @var Banner
     */
    protected $banner;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var News
     */
    protected $news;

    /**
     * HomeController constructor.
     * @param Newsletter $newsletter
     * @param Banner $banner
     * @param Product $product
     * @param News $news
     */
    public function __construct(Newsletter $newsletter, Banner $banner, Product $product, News $news)
    {
        $this->newsletter = $newsletter;
        $this->banner = $banner;
        $this->product = $product;
        $this->news = $news;
    }

    /**
     * Homepage
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $banners = $this->banner->whereActive(1)->whereBannerType('banner')->select(['id', 'title', 'sub_title', 'image', 'url', 'target'])->get()->toArray();

        $product_new = $this->product->whereActive('published')->orderBy('id', 'desc')->take(8)->get();
        $product_sale = $this->product->whereActive('published')->orderBy('sale', 'desc')->take(8)->get();
        $product_trending = $this->product->whereActive('published')->orderBy('views', 'desc')->take(8)->get();

        $news = $this->news->whereActive('published')->orderBy('id', 'desc')->take(3)->get();

        return view('client.home')->with([
            'banners' => $banners,
            'product_new' => $product_new,
            'product_sale' => $product_sale,
            'product_trending' => $product_trending,
            'news' => $news,
        ]);
    }

    /**
     * Register newsletter
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function newsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:newsletters,email']
        ], [
            'email.required' => 'Email là trường bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã đăng ký trước đó.',
        ]);

        if ($validator->fails()) {
            hwa_notify_error($validator->getMessageBag()->first());
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $this->newsletter->create([
                'email' => strtolower($request['email'])
            ]);
            hwa_notify_success("Đăng ký nhận bản tin thành công.");
            return redirect()->back();
        }
    }
}
