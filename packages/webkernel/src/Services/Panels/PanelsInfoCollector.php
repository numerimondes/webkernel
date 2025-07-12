<?php
namespace Webkernel\Services\Panels;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class PanelsInfoCollector extends ServiceProvider
{
    public static function getAllPanelsInfo(): array
    {
        $panels = Filament::getPanels();
        $infos = [];
        
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, 'Filament\PanelProvider') && method_exists($class, 'webkernelPanelInfo')) {
                $infos[] = $class::webkernelPanelInfo();
            }
        }
        
        foreach ($panels as $panel) {
            if (!collect($infos)->where('id', $panel->getId())->count()) {
                $defaultInfo = [
                    'id' => $panel->getId(),
                    'path' => $panel->getPath(),
                ];
                
                $allowedMethods = ['getName', 'getUrl', 'getDomain', 'getColors', 'getFont', 'getFontFamily', 'getFontProvider', 'getFontUrl'];
                
                foreach (get_class_methods($panel) as $method) {
                    if (in_array($method, $allowedMethods)) {
                        try {
                            $value = $panel->$method();
                            if ($value !== null && !is_object($value) && !is_array($value)) {
                                $key = strtolower(substr($method, 3));
                                $defaultInfo[$key] = $value;
                            }
                        } catch (\Exception $e) {
                            // Skip methods that cause errors
                        }
                    }
                }
                
                $infos[] = $defaultInfo;
            }
        }
        
        return $infos;
    }
}