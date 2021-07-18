<?php

namespace App\Widgets;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Widgets\BaseDimmer;
use App\Models\Tower;

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
        $count = Tower::count();
        $string =  "Towers";//trans_choice('voyager::dimmer.user', $count);

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-lighthouse',
            'title'  => "{$count} {$string}",
            'text'   =>"Tower" ,//__('voyager::dimmer.user_text', ['count' => $count, 'string' => Str::lower($string)]),
            'button' => [
                'text' => "Tower",
                'link' => route('voyager.tower.index'),
            ],
            'image' => 'Towers.jpg',//voyager_asset('images/widget-backgrounds/01.jpg'),
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