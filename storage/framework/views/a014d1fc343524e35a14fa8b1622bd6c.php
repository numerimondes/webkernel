<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
/**
 * Tab configuration
 */
$tabs = [
    ['id' => 'flight', 'icon' => 'isax-airplane5', 'label' => 'Flights', 'active' => true],
    ['id' => 'hotels', 'icon' => 'isax-buildings5', 'label' => 'Hotels', 'active' => false],
    ['id' => 'cars', 'icon' => 'isax-car5', 'label' => 'Cars', 'active' => false],
    ['id' => 'cruise', 'icon' => 'isax-ship5', 'label' => 'Cruise', 'active' => false],
    ['id' => 'tour', 'icon' => 'isax-camera4', 'label' => 'Tour', 'active' => false],
];

/**
 * Common data
 */
$locations = [
    ['city' => 'Newyork', 'airport' => 'Ken International Airport'],
    ['city' => 'Boston', 'airport' => 'Boston Logan International Airport'],
    ['city' => 'Northern Virginia', 'airport' => 'Dulles International Airport'],
    ['city' => 'Los Angeles', 'airport' => 'Los Angeles International Airport'],
    ['city' => 'Orlando', 'airport' => 'Orlando International Airport'],
];

$countries = [
    ['name' => 'USA', 'count' => '2000 Properties'],
    ['name' => 'Japan', 'count' => '3000 Properties'],
    ['name' => 'Singapore', 'count' => '8000 Properties'],
    ['name' => 'Russia', 'count' => '8000 Properties'],
    ['name' => 'Germany', 'count' => '2000 Properties'],
];

$carLocations = [
    ['name' => 'USA', 'count' => '2000 Cars'],
    ['name' => 'Japan', 'count' => '3000 Cars'],
    ['name' => 'Singapore', 'count' => '8000 Cars'],
    ['name' => 'Russia', 'count' => '8000 Cars'],
    ['name' => 'Germany', 'count' => '6000 Cars'],
];

$tourLocations = [
    ['name' => 'USA', 'count' => '200 Places'],
    ['name' => 'Japan', 'count' => '300 Places'],
    ['name' => 'Singapore', 'count' => '80 Places'],
    ['name' => 'Russia', 'count' => '500 Places'],
    ['name' => 'Germany', 'count' => '150 Places'],
];

$cabinClasses = [
    ['id' => 'economy', 'label' => 'Economy', 'checked' => true],
    ['id' => 'premium-economy', 'label' => 'Premium Economy', 'checked' => false],
    ['id' => 'business', 'label' => 'Business', 'checked' => false],
    ['id' => 'first-class', 'label' => 'First Class', 'checked' => false],
];

$roomTypes = [
    ['id' => 'room1', 'label' => 'Single', 'checked' => true],
    ['id' => 'room2', 'label' => 'Double', 'checked' => false],
    ['id' => 'room3', 'label' => 'Delux', 'checked' => false],
    ['id' => 'room4', 'label' => 'Suite', 'checked' => false],
];

$cabinTypes = [
    ['id' => 'cabin1', 'label' => 'Solo cabins', 'checked' => true],
    ['id' => 'cabin2', 'label' => 'Balcony', 'checked' => false],
    ['id' => 'cabin3', 'label' => 'Oceanview', 'checked' => false],
    ['id' => 'cabin4', 'label' => 'Balcony rooms', 'checked' => false],
];

$tripTypes = [
    ['id' => 'oneway', 'label' => 'Oneway', 'checked' => true],
    ['id' => 'roundtrip', 'label' => 'Round Trip', 'checked' => false],
    ['id' => 'multiway', 'label' => 'Multi Trip', 'checked' => false],
];

$dropOffTypes = [
    ['id' => 'same-drop', 'label' => 'Same drop-off', 'checked' => true],
    ['id' => 'different-drop', 'label' => 'Different Drop off', 'checked' => false],
    ['id' => 'airport', 'label' => 'Airport', 'checked' => false],
    ['id' => 'hourly-drop', 'label' => 'Hourly Package', 'checked' => false],
];

$travelerTypes = [
    ['label' => 'Adults ( 12+ Yrs )', 'default' => 2],
    ['label' => 'Childrens ( 2-12 Yrs )', 'default' => 0],
    ['label' => 'Infants ( 0-2 Yrs )', 'default' => 0],
];

/**
 * Render location dropdown
 */
function renderLocationDropdown($label, $defaultValue, $subtitle, $items, $placeholder, $isIsEnd = false) {
    $itemsHtml = '';
    foreach ($items as $item) {
        $title = $item['city'] ?? $item['name'];
        $sub = $item['airport'] ?? $item['count'];
        $itemsHtml .= "<li class='border-bottom'><a class='dropdown-item' href='javascript:void(0);'><h6 class='text-base font-medium'>{$title}</h6><p class='text-sm text-gray-500'>{$sub}</p></a></li>";
    }

    $arrow = $isIsEnd ? '<span class="way-icon badge badge-primary rounded-pill translate-middle"><i class="fa-solid fa-arrow-right-arrow-left"></i></span>' : '';

    return <<<HTML
        <div class="form-item dropdown">
            <div data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <label class="form-label text-sm text-gray-700 mb-1">{$label}</label>
                <input type="text" class="form-control" value="{$defaultValue}" />
                <p class="text-xs text-gray-500 mb-0">{$subtitle}</p>
                {$arrow}
            </div>
            <div class="dropdown-menu dropdown-md p-0">
                <div class="input-search p-3 border-bottom">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="{$placeholder}" />
                        <span class="input-group-text px-2 border-start-0"><i class="isax isax-search-normal"></i></span>
                    </div>
                </div>
                <ul>
                    {$itemsHtml}
                </ul>
            </div>
        </div>
    HTML;
}

/**
 * Render date input
 */
function renderDateInput($label, $value, $dayLabel) {
    return <<<HTML
        <div class="form-item">
            <label class="form-label text-sm text-gray-700 mb-1">{$label}</label>
            <input type="text" class="form-control datetimepicker" value="{$value}" />
            <p class="text-xs text-gray-500 mb-0">{$dayLabel}</p>
        </div>
    HTML;
}

/**
 * Render time input
 */
function renderTimeInput($label, $value) {
    return <<<HTML
        <div class="form-item">
            <label class="form-label text-sm text-gray-700 mb-1">{$label}</label>
            <input type="text" class="form-control timepicker" value="{$value}" />
        </div>
    HTML;
}

/**
 * Render traveler dropdown
 */
function renderTravelerDropdown($label, $persons, $detail, $travelerTypes, $classes = [], $className = 'cabin_class') {
    $typesHtml = '';
    foreach ($travelerTypes as $type) {
        $typesHtml .= renderTravelerCounter($type['label'], $type['default']);
    }

    $classesHtml = '';
    foreach ($classes as $cls) {
        $checked = $cls['checked'] ? 'checked' : '';
        $classesHtml .= "<div class='form-check me-3 mb-3'><input class='form-check-input' type='radio' name='{$className}' id='{$cls['id']}' {$checked} /><label class='form-check-label' for='{$cls['id']}'>{$cls['label']}</label></div>";
    }

    $classSection = !empty($classes) ? "<div class='mb-3 border rounded-lg p-3'><h6 class='text-base font-medium mb-2'>Select Class</h6><div class='flex flex-wrap'>{$classesHtml}</div></div>" : '';

    return <<<HTML
        <div class="form-item dropdown">
            <div data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <label class="form-label text-sm text-gray-700 mb-1">{$label}</label>
                <h5>{$persons} <span class="font-normal text-sm">Persons</span></h5>
                <p class="text-xs text-gray-500 mb-0">{$detail}</p>
            </div>
            <div class="dropdown-menu dropdown-menu-end dropdown-xl">
                <h5 class="mb-3 p-4 text-lg font-semibold">Select Travelers & Class</h5>
                <div class="p-4 border-b">
                    <h6 class="text-base font-medium mb-2">Travellers</h6>
                    <div class="grid grid-cols-3 gap-4">
                        {$typesHtml}
                    </div>
                </div>
                {$classSection}
                <div class="flex justify-end p-4 gap-2">
                    <a href="javascript:void(0);" class="btn btn-light btn-sm me-2">Cancel</a>
                    <button type="button" class="btn btn-primary btn-sm">Apply</button>
                </div>
            </div>
        </div>
    HTML;
}

/**
 * Render traveler counter
 */
function renderTravelerCounter($label, $value) {
    return <<<HTML
        <div class="mb-3">
            <label class="text-sm text-gray-700 mb-2">{$label}</label>
            <div class="flex border rounded overflow-hidden">
                <button type="button" class="p-2 text-gray-500 hover:text-gray-700 quantity-left-minus" data-type="minus">
                    <i class="isax isax-minus"></i>
                </button>
                <input type="text" class="w-12 text-center border-0 input-number" value="{$value}" />
                <button type="button" class="p-2 text-gray-500 hover:text-gray-700 quantity-right-plus" data-type="plus">
                    <i class="isax isax-add"></i>
                </button>
            </div>
        </div>
    HTML;
}

/**
 * Render radio options
 */
function renderRadioOptions($options, $name) {
    $html = '';
    foreach ($options as $option) {
        $checked = $option['checked'] ? 'checked' : '';
        $html .= "<div class='form-check d-flex align-items-center me-3 mb-2'><input class='form-check-input mt-0' type='radio' name='{$name}' id='{$option['id']}' value='{$option['id']}' {$checked} /><label class='form-check-label text-sm ms-2' for='{$option['id']}'>{$option['label']}</label></div>";
    }
    return $html;
}
?>

<div class="banner-form card mb-0">
    <div class="card-header">
        <ul class="nav">
            <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <a href="javascript:void(0);"
                       class="nav-link <?php echo e($tab['active'] ? 'active' : ''); ?>"
                       data-bs-toggle="tab"
                       data-bs-target="#<?php echo e($tab['id']); ?>">
                        <i class="isax isax-<?php echo e($tab['icon']); ?> me-2"></i><?php echo e($tab['label']); ?>

                    </a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <!-- Flight Tab -->
            <div class="tab-pane fade <?php echo e($tabs[0]['active'] ? 'active show' : ''); ?>" id="<?php echo e($tabs[0]['id']); ?>">
                <form action="https://numerimondes.com/html/flight-grid.html">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                        <div class="d-flex align-items-center flex-wrap">
                            <?php echo renderRadioOptions($tripTypes, 'Radio'); ?>

                        </div>
                        <h6 class="fw-medium fs-16 mb-2">Millions of cheap flights. One simple search</h6>
                    </div>
                    <div class="normal-trip">
                        <div class="d-lg-flex">
                            <div class="d-flex form-info">
                                <?php echo renderLocationDropdown('From', 'Newyork', 'Ken International Airport', $locations, 'Search Location'); ?>

                                <?php echo renderLocationDropdown('To', 'Las Vegas', 'Martini International Airport', $locations, 'Search Location', true); ?>

                                <?php echo renderDateInput('Departure', '07-09-2025', 'Sunday'); ?>

                                <div class="form-item round-drip">
                                    <?php echo renderDateInput('Return', '23-10-2024', 'Wednesday'); ?>

                                </div>
                                <?php echo renderTravelerDropdown('Travellers and cabin class', '4', '1 Adult, Economy', $travelerTypes, $cabinClasses); ?>

                            </div>
                            <button type="submit" class="btn btn-primary search-btn rounded">Search</button>
                        </div>
                    </div>
                    <div class="multi-trip" style="display: none;">
                        <div class="d-lg-flex">
                            <div class="d-flex form-info">
                                <?php echo renderLocationDropdown('From', 'Newyork', 'Ken International Airport', $locations, 'Search Location'); ?>

                                <?php echo renderLocationDropdown('To', 'Las Vegas', 'Martini International Airport', $locations, 'Search Location', true); ?>

                                <?php echo renderDateInput('Departure', '21-10-2024', 'Monday'); ?>

                            </div>
                            <button type="submit" class="btn btn-primary search-btn rounded">Search</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Hotels Tab -->
            <div class="tab-pane fade" id="<?php echo e($tabs[1]['id']); ?>">
                <form action="https://numerimondes.com/html/hotel-grid.html">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <h6 class="fw-medium fs-16 mb-2">Book Hotel - Villas, Apartments & more.</h6>
                    </div>
                    <div class="d-lg-flex">
                        <div class="d-flex form-info">
                            <?php echo renderLocationDropdown('City, Property name or Location', 'Newyork', 'USA', $countries, 'Search for City, Property name or Location'); ?>

                            <?php echo renderDateInput('Check In', '21-10-2025', 'Monday'); ?>

                            <?php echo renderDateInput('Check Out', '21-10-2025', 'Monday'); ?>

                            <?php echo renderTravelerDropdown('Guests', '4', '4 Adult, 2 Rooms', [['label' => 'Rooms', 'default' => 1], ['label' => 'Adults', 'default' => 2], ['label' => 'Childrens ( 2-12 Yrs )', 'default' => 0], ['label' => 'Infants ( 0-12 Yrs )', 'default' => 0]], $roomTypes, 'room'); ?>

                            <div class="form-item dropdown">
                                <div data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <label class="form-label text-sm text-gray-700 mb-1">Price per Night</label>
                                    <input type="text" class="form-control" value="$1000 - $15000" />
                                    <p class="text-xs text-gray-500 mb-0">20 Offers Available</p>
                                </div>
                                <div class="dropdown-menu dropdown-md p-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <h6 class="text-base font-medium">$500 - $2000</h6>
                                                <p class="text-sm text-gray-500">Upto 65% offers</p>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <h6 class="text-base font-medium">$5000 - $8000</h6>
                                                <p class="text-sm text-gray-500">Upto 40% offers</p>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <h6 class="text-base font-medium">$9000 - $11000</h6>
                                                <p class="text-sm text-gray-500">Upto 35% offers</p>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <h6 class="text-base font-medium">$11000 - $15000</h6>
                                                <p class="text-sm text-gray-500">Upto 20% offers</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);">
                                                <h6 class="text-base font-medium">$15000+</h6>
                                                <p class="text-sm text-gray-500">Upto 10% offers</p>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary search-btn rounded">Search</button>
                    </div>
                </form>
            </div>

            <!-- Cars Tab -->
            <div class="tab-pane fade" id="<?php echo e($tabs[2]['id']); ?>">
                <form action="https://numerimondes.com/html/car-grid.html">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                        <div class="d-flex align-items-center flex-wrap">
                            <?php echo renderRadioOptions($dropOffTypes, 'drop'); ?>

                        </div>
                        <h6 class="fw-medium fs-16 mb-2">Book Car for rental</h6>
                    </div>
                    <div class="d-lg-flex">
                        <div class="d-flex form-info">
                            <?php echo renderLocationDropdown('From', 'Newyork', 'USA', $carLocations, 'Search for Cars'); ?>

                            <?php echo renderLocationDropdown('To', 'Newyork', 'USA', $carLocations, 'Search for Cars', true); ?>

                            <?php echo renderDateInput('Departure', '21-10-2024', 'Monday'); ?>

                            <div class="form-item return-drop">
                                <?php echo renderDateInput('Return', '23-10-2024', 'Wednesday'); ?>

                            </div>
                            <?php echo renderTimeInput('Pickup Time', '11:45 PM'); ?>

                            <?php echo renderTimeInput('Dropoff Time', '11:45 PM'); ?>

                            <div class="form-item hourly-time">
                                <label class="form-label text-sm text-gray-700 mb-1">Hours</label>
                                <h5 class="text-base font-semibold">02 Hrs 10 Kms</h5>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary search-btn rounded">Search</button>
                    </div>
                </form>
            </div>

            <!-- Cruise Tab -->
            <div class="tab-pane fade" id="<?php echo e($tabs[3]['id']); ?>">
                <form action="https://numerimondes.com/html/cruise-grid.html">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                        <h6 class="fw-medium fs-16 mb-2">Search For Cruise</h6>
                    </div>
                    <div class="d-lg-flex">
                        <div class="d-flex form-info">
                            <?php echo renderLocationDropdown('Destination', 'Newyork', 'USA', $locations, 'Search Location'); ?>

                            <?php echo renderDateInput('Start Date', '21-10-2025', 'Monday'); ?>

                            <?php echo renderDateInput('End Date', '21-10-2025', 'Monday'); ?>

                            <?php echo renderTravelerDropdown('Travellers & Cabin', '4', '4 Adult, 2 Rooms', $travelerTypes, $cabinTypes, 'cabin'); ?>

                        </div>
                        <button type="submit" class="btn btn-primary search-btn rounded">Search</button>
                    </div>
                </form>
            </div>

            <!-- Tour Tab -->
            <div class="tab-pane fade" id="<?php echo e($tabs[4]['id']); ?>">
                <form action="https://numerimondes.com/html/tour-grid.html">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                        <h6 class="fw-medium fs-16 mb-2">Search holiday packages & trips</h6>
                    </div>
                    <div class="d-lg-flex">
                        <div class="d-flex form-info">
                            <?php echo renderLocationDropdown('Where would like to go?', 'Newyork', 'USA', $tourLocations, 'Search for City, Property name or Location'); ?>

                            <?php echo renderDateInput('Dates', '21-10-2025', 'Monday'); ?>

                            <?php echo renderDateInput('Check Out', '21-10-2025', 'Wednesday'); ?>

                            <?php echo renderTravelerDropdown('Travellers', '4', '2 Adult', $travelerTypes); ?>

                        </div>
                        <button type="submit" class="btn btn-primary search-btn rounded">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr('.datetimepicker', {
        enableTime: false,
        dateFormat: 'd-m-Y',
    });
    flatpickr('.timepicker', {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i K',
        time_24hr: false,
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/yassine/Documents/project/numerimondes-com/platform/EnjoyTheWorld/Resources/Views/software/body/01-1-hero-section-card.blade.php ENDPATH**/ ?>