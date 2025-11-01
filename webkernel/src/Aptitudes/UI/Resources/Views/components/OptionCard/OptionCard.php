<?php
declare(strict_types=1);
namespace Webkernel\Aptitudes\UI\Resources\Views\components\OptionCard;
use Webkernel\Aptitudes\UI\ComponentBase;
use Webkernel\Aptitudes\UI\ComponentSchema;

class OptionCard extends ComponentBase
{
    /**
     * Schema for OptionCard
     * return void
     */
    protected function define(ComponentSchema $schema): void
    {
        $schema
            // Propriétés de base
            ->string("href")
            ->nullable()
            ->boolean("disabled")
            ->default(false)
            ->string("target")
            ->default("_self")
            ->string("icon")
            ->nullable()
            ->string("title")
            ->default("Card Title")
            ->string("description")
            ->nullable()
            ->string("badge")
            ->nullable()
            ->mixed("notification")
            ->nullable()
            ->string("color")
            ->default("gray")
            ->array("cards")
            ->nullable()
            ->number("columns")
            ->default(2)
            ->string("gap")
            ->default("16")

            // Padding du conteneur (legacy + nouveaux)
            ->string("container-padding")
            ->default("0")
            ->string("padding-top")
            ->nullable()
            ->string("padding-bottom")
            ->nullable()
            ->string("padding-left")
            ->nullable()
            ->string("padding-right")
            ->nullable()

            // Padding des cartes individuelles (legacy + nouveaux)
            ->string("padding")
            ->default("12")
            ->string("card-padding-top")
            ->nullable()
            ->string("card-padding-bottom")
            ->nullable()
            ->string("card-padding-left")
            ->nullable()
            ->string("card-padding-right")
            ->nullable()

            // Propriétés calculées
            ->compute("tag", fn($config) => $config["href"] ? "a" : "div")
            ->compute("isMultiple", fn($config) => !empty($config["cards"]))

            ->compute("iconColor", function ($config) {
                return match ($config["color"]) {
                    "primary" => "var(--primary-600, #2563eb)",
                    "success" => "var(--success-600, #16a34a)",
                    "warning" => "var(--warning-600, #ca8a04)",
                    "danger" => "var(--danger-600, #dc2626)",
                    "info" => "var(--info-600, #0284c7)",
                    "gray" => "var(--gray-600, #4b5563)",
                    default => "var(--gray-600, #4b5563)",
                };
            })

            ->compute("badgeStyle", function ($config) {
                $styles = match ($config["color"]) {
                    "primary" => [
                        "bg" => "var(--primary-100, #dbeafe)",
                        "text" => "var(--primary-800, #1e40af)",
                    ],
                    "success" => [
                        "bg" => "var(--success-100, #dcfce7)",
                        "text" => "var(--success-800, #166534)",
                    ],
                    "warning" => [
                        "bg" => "var(--warning-100, #fef3c7)",
                        "text" => "var(--warning-800, #92400e)",
                    ],
                    "danger" => [
                        "bg" => "var(--danger-100, #fee2e2)",
                        "text" => "var(--danger-800, #991b1b)",
                    ],
                    "info" => [
                        "bg" => "var(--info-100, #e0f2fe)",
                        "text" => "var(--info-800, #075985)",
                    ],
                    "gray" => [
                        "bg" => "var(--gray-100, #f3f4f6)",
                        "text" => "var(--gray-800, #1f2937)",
                    ],
                    default => [
                        "bg" => "var(--gray-100, #f3f4f6)",
                        "text" => "var(--gray-800, #1f2937)",
                    ],
                };

                return "background-color: {$styles["bg"]}; color: {$styles["text"]};";
            })

            ->compute("notificationBg", function ($config) {
                return match ($config["color"]) {
                    "primary" => "var(--primary-500, #3b82f6)",
                    "success" => "var(--success-500, #22c55e)",
                    "warning" => "var(--warning-500, #eab308)",
                    "danger" => "var(--danger-500, #ef4444)",
                    "info" => "var(--info-500, #06b6d4)",
                    "gray" => "var(--gray-500, #6b7280)",
                    default => "var(--gray-500, #6b7280)",
                };
            })

            // Gestion des padding calculés
            ->compute("containerPaddingTop", function ($config) {
                return $config["padding-top"] ?? $config["container-padding"];
            })
            ->compute("containerPaddingBottom", function ($config) {
                return $config["padding-bottom"] ?? $config["container-padding"];
            })
            ->compute("containerPaddingLeft", function ($config) {
                return $config["padding-left"] ?? $config["container-padding"];
            })
            ->compute("containerPaddingRight", function ($config) {
                return $config["padding-right"] ?? $config["container-padding"];
            })

            ->compute("cardPaddingTop", function ($config) {
                return $config["card-padding-top"] ?? $config["padding"];
            })
            ->compute("cardPaddingBottom", function ($config) {
                return $config["card-padding-bottom"] ?? $config["padding"];
            })
            ->compute("cardPaddingLeft", function ($config) {
                return $config["card-padding-left"] ?? $config["padding"];
            })
            ->compute("cardPaddingRight", function ($config) {
                return $config["card-padding-right"] ?? $config["padding"];
            })

            // Attributs conditionnels
            ->conditionalAttribute("href", fn($config) => $config["tag"] === "a" ? $config["href"] : null)
            ->conditionalAttribute("target", fn($config) => $config["tag"] === "a" ? $config["target"] : null)
            ->conditionalAttribute("disabled", "disabled", true);
    }
}
