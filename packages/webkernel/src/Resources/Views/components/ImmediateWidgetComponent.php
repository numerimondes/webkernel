<?php

namespace Webkernel\Resources\Views\Components;

use Illuminate\View\View;
use Closure;
use Illuminate\View\Component;
use Filament\Widgets\Widget;
use Livewire\Livewire;

class ImmediateWidgetComponent extends Component
{
    /**
     * Widget class
     *
     * @var string
     */
    public string $widget;

    /**
     * Create the component instance.
     *
     * @param string $widget
     * @return void
     */
    public function __construct(string $widget)
    {
        $this->widget = $widget;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Closure|string
     */

    public function render()
    {
        if (!class_exists($this->widget) || !is_subclass_of($this->widget, Widget::class)) {
            return '';
        }

        $alias = $this->getLivewireAlias($this->widget);

        return Livewire::test($alias)->html();
    }

    /**
     * Get Livewire alias for widget
     *
     * @param string $widgetClass
     * @return string
     */
    protected function getLivewireAlias(string $widgetClass): string
    {
        $parts = explode('\\', $widgetClass);
        $className = end($parts);

        $alias = preg_replace('/([a-z])([A-Z])/', '$1-$2', $className);
        $alias = strtolower($alias);

        return 'webkernel.filament.widgets.' . $alias;
    }
}
