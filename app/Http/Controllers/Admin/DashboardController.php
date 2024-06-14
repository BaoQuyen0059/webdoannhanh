<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\News;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $month;

    protected $year;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var News
     */
    protected $news;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var OrderDetail
     */
    protected $order_detail;

    /**
     * DashboardController constructor.
     * @param Customer $customer
     * @param News $news
     * @param Product $product
     * @param Order $order
     * @param OrderDetail $order_detail
     */
    public function __construct(Customer $customer, News $news, Product $product, Order $order, OrderDetail $order_detail)
    {
        $this->product = $product;
        $this->customer = $customer;
        $this->news = $news;
        $this->order = $order;
        $this->order_detail = $order_detail;
        $this->month = Carbon::now()->month;
        $this->year = Carbon::now()->year;
    }

    /**
     * Get dashboard
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $card = [
            'product' => $this->product->select(['id'])->get()->count(),
            'customer' => $this->customer->select(['id'])->get()->count(),
            'order' => $this->order->select(['id'])->whereActive('pending')->get()->count(),
            'news' => $this->news->select(['id'])->get()->count(),
        ];

        // Order information
        $orders = $this->cardOrder($this->month, $this->year);

        // Order chart in current year
        $chart = [
            'pending' => $this->genChart('pending', $this->year),
            'processing' => $this->genChart('processing', $this->year),
            'cancel' => $this->genChart('cancel', $this->year),
            'done' => $this->genChart('done', $this->year),
            'fail' => $this->genChart('fail', $this->year),
        ];

        return view('admin.home')->with([
            'card' => $card,
            'orders' => $orders,
            'chart' => $chart,
            'newestOrders' => $this->order->whereActive('pending')->take(10)->get()
        ]);
    }

    /**
     * Card order information
     *
     * @param $month
     * @param $year
     * @return array
     */
    public function cardOrder($month, $year)
    {
        $done = $this->order->whereActive('done')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $dateS = Carbon::now()->startOfMonth()->subMonth(1);
        $dateE = Carbon::now()->startOfMonth();

        $lastSuccess = $this->order->whereActive('done')->whereBetween('created_at',[$dateS,$dateE])->get();

        $pending = $this->order->whereActive('pending')->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $processing = $this->order->whereActive('processing')->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $fail = $this->order->whereActive('fail')->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $cancel = $this->order->whereActive('cancel')->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->count();

        $orderSuccess = $done->count();
        $lastOrderSuccess = $lastSuccess->count();

        $orderUp = $orderSuccess - $lastOrderSuccess;

        $currentRevenue = $this->revenue($done);
        $lastRevenue = $this->revenue($lastSuccess);

        return [
            'revenue' => $currentRevenue,
            'revenueLast' => $lastRevenue,
            'revenueUpOrDown' => $currentRevenue - $lastRevenue,
            'done' => $orderSuccess,
            'pending' => $pending,
            'processing' => $processing,
            'cancel' => $cancel,
            'fail' => $fail,
            'orderUp' => $orderUp
        ];
    }

    /**
     * Calculator revenue
     *
     * @param $order
     * @return float|int
     */
    public function revenue($order)
    {
        $total = 0;
        foreach ($order as $item) {
            $orderDetails = $this->order_detail->whereOrderId($item->id)->get();
            $revenue = 0;
            foreach ($orderDetails as $key) {
                $revenue += (($key->qty * $key->price) - ($key->qty * $key->product->unit_price));
            }
            $total += $revenue;
        }
        return $total;
    }

    /**
     * Generate value order in current year.
     *
     * @param $status
     * @param $year
     * @return string
     */
    public function genChart($status, $year)
    {
        $data = [];
        for ($id = 1; $id <= 12; $id++) {
            $order = $this->order->whereActive($status)
                ->whereMonth('created_at', $id)
                ->whereYear('created_at', $year)
                ->count();
            array_push($data, $order);
        }
        return implode(',', $data);
    }
}
