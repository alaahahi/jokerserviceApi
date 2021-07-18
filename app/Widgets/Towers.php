<?php

namespace App\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Widgets\BaseDimmer;
use App\Models\Order;

class Towers extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $count = Order::count();
        $string =  "Orders";//trans_choice('voyager::dimmer.user', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-lighthouse',
            'title'  => "{$count} {$string}",
            'text'   =>"Order" ,//__('voyager::dimmer.user_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => "Order",
                'link' => route('voyager.order.index'),
            ],
            'image' => voyager_asset('images/widget-backgrounds/03.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return 1;//Auth::user()->can('browse',Project::count());
    }
}