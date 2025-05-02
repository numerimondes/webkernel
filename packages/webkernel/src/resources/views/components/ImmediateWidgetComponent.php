<?php

namespace Webkernel\resources\views\components;

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
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        // Check if widget class exists and is a subclass of Widget
        if (!class_exists($this->widget) || !is_subclass_of($this->widget, Widget::class)) {
            return '';
        }

        // Get the Livewire alias for the widget
        $alias = $this->getLivewireAlias($this->widget);

        // Render the widget synchronously
        return Livewire::mount($alias)->html();
    }

    /**
     * Get Livewire alias for widget
     *
     * @param string $widgetClass
     * @return string
     */
    protected function getLivewireAlias(string $widgetClass): string
    {
        // Extract class name from the fully qualified class name
        $parts = explode('\\', $widgetClass);
        $className = end($parts);

        // Convert to kebab case for Livewire component name
        $alias = preg_replace('/([a-z])([A-Z])/', '$1-$2', $className);
        $alias = strtolower($alias);

        // Return the full Livewire component name
        return 'webkernel.filament.widgets.' . $alias;
    }
}
