function wbCalendarComponent(config) {
    return {
        // Configuration
        selected: config.selected,
        mode: config.mode,
        disabled: config.disabled,
        min: config.min,
        max: config.max,
        required: config.required,
        weekStart: config.weekStart,
        debug: config.debug,
        blockPast: config.blockPast,
        enableTime: config.enableTime,
        timeOptions: config.timeOptions || [],
        actionButton: config.actionButton,
        monthNames: config.monthNames,

        // Component state
        focusedDay: '',
        month: '',
        year: '',
        daysInMonth: [],
        preBlankDaysInMonth: [],
        postBlankDaysInMonth: [],
        value: null,
        selectedTime: null,
        disabledRules: [],

        init() {
            this.debugLog('=== WEBSITE BUILDER CALENDAR INIT ===');
            this.debugLog('Config:', config);

            // Initialize disabled rules
            this.initializeDisabledRules(this.disabled);

            // Set initial date
            let now = new Date();
            this.month = now.getMonth();
            this.year = now.getFullYear();
            this.focusedDay = now.getDate();

            // Initialize value based on mode
            this.initializeValue();

            this.calculateDays();

            this.debugLog('Calendar initialized successfully');
            this.debugLog('Initial value:', this.value);
            this.debugLog('=====================================');

            if (this.selected) {
                this.dispatchChange();
            }
        },

        debugLog(...args) {
            if (this.debug) {
                console.log('[WB-Calendar]', ...args);
            }
        },

        initializeValue() {
            if (this.mode === 'single') {
                this.value = this.selected ? this.createDateWithoutTime(this.selected) : null;
            } else if (this.mode === 'multiple') {
                this.value = [];
                if (this.selected && Array.isArray(this.selected)) {
                    this.selected.forEach(date => {
                        const d = this.createDateWithoutTime(date);
                        if (d) this.value.push(d);
                    });
                }
            } else if (this.mode === 'range') {
                this.value = { from: null, to: null };
                if (this.selected && typeof this.selected === 'object') {
                    if (this.selected.from) {
                        this.value.from = this.createDateWithoutTime(this.selected.from);
                    }
                    if (this.selected.to) {
                        this.value.to = this.createDateWithoutTime(this.selected.to);
                    }
                }
            } else if (this.mode === 'multi-range') {
                this.value = [];
                if (this.selected && Array.isArray(this.selected)) {
                    this.selected.forEach(range => {
                        if (range && typeof range === 'object' && range.from && range.to) {
                            this.value.push({
                                from: this.createDateWithoutTime(range.from),
                                to: this.createDateWithoutTime(range.to)
                            });
                        }
                    });
                }
            }
        },

        initializeDisabledRules(disabled) {
            this.debugLog('Initializing disabled rules:', disabled);
            this.disabledRules = [];

            if (Array.isArray(disabled)) {
                disabled.forEach(rule => {
                    this.disabledRules.push(this.createMatcher(rule));
                });
            } else if (typeof disabled === 'object' && disabled !== null) {
                this.disabledRules = [this.createMatcher(disabled)];
            }
        },

        createMatcher(rule) {
            return {
                type: this.determineMatcherType(rule),
                rule: rule,
                passes: (date) => {
                    const targetDate = this.createDateWithoutTime(date);

                    if (this.type === 'dates') {
                        return rule.dates.some(element =>
                            targetDate.getTime() === this.createDateWithoutTime(element).getTime()
                        );
                    } else if (this.type === 'range') {
                        if (rule.before && targetDate.getTime() < this.createDateWithoutTime(rule.before).getTime()) {
                            return true;
                        }
                        if (rule.after && targetDate.getTime() > this.createDateWithoutTime(rule.after).getTime()) {
                            return true;
                        }
                        return false;
                    } else if (this.type === 'dayOfWeek') {
                        if (typeof rule.dayOfWeek === 'number') {
                            return targetDate.getDay() === rule.dayOfWeek;
                        } else {
                            return rule.dayOfWeek.some(day => day === targetDate.getDay());
                        }
                    } else if (this.type === 'function') {
                        return rule(targetDate);
                    }
                    return false;
                }
            };
        },

        determineMatcherType(rule) {
            if (typeof rule === 'function') return 'function';
            if (rule.dates && Array.isArray(rule.dates)) return 'dates';
            if (rule.before !== undefined || rule.after !== undefined) return 'range';
            if (rule.dayOfWeek !== undefined) return 'dayOfWeek';
            return 'unknown';
        },

        createDateWithoutTime(value) {
            if (!value) return null;
            let date = new Date(value);
            date.setHours(0, 0, 0, 0);
            return date;
        },

        dispatchChange() {
            this.$nextTick(() => {
                this.debugLog('Dispatching change event with value:', this.value);

                // Standard events
                this.$dispatch('change', { value: this.value, time: this.selectedTime });
                this.$dispatch('wb-calendar:change', { value: this.value, time: this.selectedTime });
                this.$dispatch('input', { value: this.value, time: this.selectedTime });

                this.debugLog('Events dispatched successfully');
            });
        },

        dayClicked(day) {
            const selectedDate = new Date(this.year, this.month, day);

            if (this.isDisabled(selectedDate)) {
                this.debugLog('Day click blocked - date disabled:', selectedDate);
                return;
            }

            this.debugLog('Day clicked:', day, 'Full date:', selectedDate);
            this.focusedDay = day;

            let changed = false;

            if (this.mode === 'single') {
                changed = this.handleSingleMode(selectedDate);
            } else if (this.mode === 'multiple') {
                changed = this.handleMultipleMode(selectedDate);
            } else if (this.mode === 'range') {
                changed = this.handleRangeMode(selectedDate);
            } else if (this.mode === 'multi-range') {
                changed = this.handleMultiRangeMode(selectedDate);
            }

            if (changed) {
                this.dispatchChange();
            }
        },

        handleSingleMode(date) {
            const clickedDate = this.createDateWithoutTime(date);

            if (this.value && this.value.getTime() === clickedDate.getTime() && !this.required) {
                this.value = null;
                this.selectedTime = null;
                this.debugLog('Date deselected');
            } else {
                this.value = clickedDate;
                this.debugLog('Date selected:', this.value);
            }
            return true;
        },

        handleMultipleMode(date) {
            const clickedDate = this.createDateWithoutTime(date);
            const index = this.findDateIndex(this.value, clickedDate);

            if (index >= 0) {
                this.value.splice(index, 1);
                this.debugLog('Date removed from selection:', clickedDate);
            } else {
                // Check max constraint
                if (this.max && this.value.length >= this.max) {
                    this.debugLog('Cannot add more dates, max limit reached:', this.max);
                    return false;
                }
                this.value.push(clickedDate);
                this.debugLog('Date added to selection:', clickedDate);
            }
            return true;
        },

        handleRangeMode(date) {
            const clickedDate = this.createDateWithoutTime(date);

            if (!this.value.from || (this.value.to && this.value.to.getTime() === clickedDate.getTime())) {
                this.value.from = clickedDate;
                this.value.to = null;
                this.debugLog('Range start set:', clickedDate);
                return true;
            }

            if (this.value.from.getTime() === clickedDate.getTime()) {
                if (!this.required) {
                    this.value.from = null;
                    this.value.to = null;
                    this.debugLog('Range cleared');
                } else {
                    this.debugLog('Range kept due to required constraint');
                }
                return true;
            }

            if (this.value.from.getTime() >= clickedDate.getTime()) {
                this.value.from = clickedDate;
                this.debugLog('Range start moved to earlier date:', clickedDate);
                return true;
            }

            this.value.to = clickedDate;
            this.debugLog('Range completed:', this.value);
            return true;
        },

        handleMultiRangeMode(date) {
            const clickedDate = this.createDateWithoutTime(date);

            // Check if clicking on an existing range start/end
            for (let i = 0; i < this.value.length; i++) {
                const range = this.value[i];
                if (range.from.getTime() === clickedDate.getTime() ||
                    (range.to && range.to.getTime() === clickedDate.getTime())) {
                    this.value.splice(i, 1);
                    this.debugLog('Range removed:', range);
                    return true;
                }
            }

            // Check if clicking inside an existing range
            for (let i = 0; i < this.value.length; i++) {
                const range = this.value[i];
                if (range.to && clickedDate.getTime() > range.from.getTime() &&
                    clickedDate.getTime() < range.to.getTime()) {
                    const newRange = {
                        from: this.createDateWithoutTime(clickedDate),
                        to: this.createDateWithoutTime(range.to)
                    };
                    range.to = this.createDateWithoutTime(clickedDate);
                    this.value.push(newRange);
                    this.debugLog('Range split at:', clickedDate);
                    return true;
                }
            }

            // If we have an incomplete range, complete it
            const incompleteRange = this.value.find(range => range.from && !range.to);
            if (incompleteRange) {
                if (clickedDate.getTime() < incompleteRange.from.getTime()) {
                    incompleteRange.from = clickedDate;
                } else {
                    incompleteRange.to = clickedDate;
                }
                this.debugLog('Range completed:', incompleteRange);
                return true;
            }

            // Start a new range
            this.value.push({
                from: clickedDate,
                to: null
            });
            this.debugLog('New range started:', clickedDate);
            return true;
        },

        findDateIndex(array, targetDate) {
            const targetTime = targetDate.getTime();
            for (let i = 0; i < array.length; i++) {
                if (array[i].getTime() === targetTime) {
                    return i;
                }
            }
            return -1;
        },

        selectTime(time) {
            this.selectedTime = time;
            this.debugLog('Time selected:', time);
            this.dispatchChange();
        },

        generateActionHref() {
            if (!this.actionButton || !this.actionButton.href) return '#';

            let href = this.actionButton.href;

            // Replace date placeholder
            if (this.mode === 'single' && this.value) {
                const dateStr = this.value.toISOString().split('T')[0];
                href = href.replace('{date}', dateStr);
            }

            // Replace time placeholder
            if (this.selectedTime) {
                href = href.replace('{time}', this.selectedTime);
            }

            return href;
        },

        hasSelectedDate() {
            if (this.mode === 'single') {
                return this.value !== null;
            } else if (this.mode === 'multiple') {
                return this.value && this.value.length > 0;
            } else if (this.mode === 'range') {
                return this.value && this.value.from !== null;
            } else if (this.mode === 'multi-range') {
                return this.value && this.value.length > 0;
            }
            return false;
        },

        focusAdd(value) {
            if (!Number.isInteger(this.focusedDay)) {
                this.focusedDay = (new Date(this.year, this.month, 1)).getDate();
            }
            this.focusedDay = this.focusedDay + value;
            if (this.focusedDay > this.daysInMonth.length) {
                this.focusedDay = this.focusedDay - this.daysInMonth.length;
                this.nextMonth();
            } else if (this.focusedDay <= 0) {
                this.previousMonth();
                this.focusedDay = this.daysInMonth.length + this.focusedDay;
            }
        },

        activateFocused() {
            if (this.focusedDay && this.focusedDay > 0 && this.focusedDay <= this.daysInMonth.length) {
                this.dayClicked(this.focusedDay);
            }
        },

        previousMonth() {
            if (this.month === 0) {
                this.year--;
                this.month = 11;
            } else {
                this.month--;
            }
            this.debugLog('Navigate to:', this.monthNames[this.month], this.year);
            this.calculateDays();
        },

        nextMonth() {
            if (this.month === 11) {
                this.month = 0;
                this.year++;
            } else {
                this.month++;
            }
            this.debugLog('Navigate to:', this.monthNames[this.month], this.year);
            this.calculateDays();
        },

        isSelectedDay(day) {
            const date = new Date(this.year, this.month, day);
            const targetTime = this.createDateWithoutTime(date).getTime();

            this.debugLog(`Checking if day ${day} is selected:`, {
                date: date,
                targetTime: targetTime,
                value: this.value,
                mode: this.mode
            });

            if (this.mode === 'single') {
                const isSelected = this.value && this.value.getTime() === targetTime;
                this.debugLog(`Single mode - isSelected: ${isSelected}`, {
                    valueTime: this.value ? this.value.getTime() : null,
                    targetTime: targetTime
                });
                return isSelected;
            } else if (this.mode === 'multiple') {
                return this.value && this.value.some(d => d.getTime() === targetTime);
            } else if (this.mode === 'range') {
                if (!this.value || !this.value.from) return false;
                if (!this.value.to) {
                    return this.value.from.getTime() === targetTime;
                }
                return targetTime === this.value.from.getTime() || targetTime === this.value.to.getTime();
            } else if (this.mode === 'multi-range') {
                return this.value && this.value.some(range => {
                    if (!range.from) return false;
                    if (!range.to) {
                        return range.from.getTime() === targetTime;
                    }
                    return targetTime === range.from.getTime() || targetTime === range.to.getTime();
                });
            }
            return false;
        },

        isRangeStart(day) {
            if (this.mode !== 'range' && this.mode !== 'multi-range') return false;

            const date = new Date(this.year, this.month, day);
            const targetTime = this.createDateWithoutTime(date).getTime();

            if (this.mode === 'range') {
                return this.value && this.value.from && this.value.from.getTime() === targetTime;
            } else if (this.mode === 'multi-range') {
                return this.value && this.value.some(range =>
                    range.from && range.from.getTime() === targetTime
                );
            }
            return false;
        },

        isRangeEnd(day) {
            if (this.mode !== 'range' && this.mode !== 'multi-range') return false;

            const date = new Date(this.year, this.month, day);
            const targetTime = this.createDateWithoutTime(date).getTime();

            if (this.mode === 'range') {
                return this.value && this.value.to && this.value.to.getTime() === targetTime;
            } else if (this.mode === 'multi-range') {
                return this.value && this.value.some(range =>
                    range.to && range.to.getTime() === targetTime
                );
            }
            return false;
        },

        isRangeMiddle(day) {
            if (this.mode !== 'range' && this.mode !== 'multi-range') return false;

            const date = new Date(this.year, this.month, day);
            const targetTime = this.createDateWithoutTime(date).getTime();

            if (this.mode === 'range') {
                return this.value && this.value.from && this.value.to &&
                       targetTime > this.value.from.getTime() &&
                       targetTime < this.value.to.getTime();
            } else if (this.mode === 'multi-range') {
                return this.value && this.value.some(range => {
                    return range.from && range.to &&
                           targetTime > range.from.getTime() &&
                           targetTime < range.to.getTime();
                });
            }
            return false;
        },

        isToday(day) {
            const today = new Date();
            const date = new Date(this.year, this.month, day);
            return today.toDateString() === date.toDateString();
        },

        isDisabled(date) {
            // Check if past dates should be blocked
            if (this.blockPast) {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (date < today) {
                    return true;
                }
            }

            // Check custom disabled rules
            for (let rule of this.disabledRules) {
                if (rule.passes(date)) {
                    return true;
                }
            }

            // Check min/max constraints for range modes
            if (this.mode === 'range' && this.value && this.value.from) {
                const daysBetween = Math.abs(this.getNumberOfDaysBetweenDates(this.value.from, date));
                if (((this.min && daysBetween < this.min) || (this.max && daysBetween > this.max)) && daysBetween !== 0) {
                    return true;
                }
            }

            // Check max constraint for multiple mode
            if (this.mode === 'multiple' && this.max && this.value && this.value.length >= this.max) {
                return !this.isSelectedDay(new Date(this.year, this.month, date.getDate()).getDate());
            }

            return false;
        },

        getNumberOfDaysBetweenDates(date1, date2) {
            return Math.round((date1.getTime() - date2.getTime()) / (1000 * 3600 * 24));
        },

        calculateDays() {
            const daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
            const daysInPreviousMonth = new Date(this.year, this.month, 0).getDate();

            // Adjust for week start
            let dayOfWeek = new Date(this.year, this.month).getDay();
            dayOfWeek = (dayOfWeek - this.weekStart + 7) % 7;

            let preBlankdaysArray = [];
            for (let i = 1; i <= dayOfWeek; i++) {
                preBlankdaysArray.push(daysInPreviousMonth - i + 1);
            }
            preBlankdaysArray = preBlankdaysArray.reverse();

            let postBlankdaysArray = [];
            for (let i = 1; i <= (7 * 6 - (preBlankdaysArray.length + daysInMonth)); i++) {
                postBlankdaysArray.push(i);
            }

            let daysArray = [];
            for (let i = 1; i <= daysInMonth; i++) {
                daysArray.push(i);
            }

            this.preBlankDaysInMonth = preBlankdaysArray;
            this.postBlankDaysInMonth = postBlankdaysArray;
            this.daysInMonth = daysArray;

            this.debugLog('Calendar calculated - Days:', daysArray.length, 'Pre-blank:', preBlankdaysArray.length, 'Post-blank:', postBlankdaysArray.length);
        }
    };
}

// Register with Alpine.js
document.addEventListener('alpine:init', () => {
    console.log('[Website Builder] Alpine init event fired, registering calendar component');
    Alpine.data('wbCalendarComponent', wbCalendarComponent);

    // Global Website Builder component registry
    if (!window.WBComponents) {
        window.WBComponents = {};
    }

    window.WBComponents.calendar = {
        name: 'Calendar',
        version: '1.0.0',
        type: 'form',
        category: 'date-inputs',
        supports: ['single', 'multiple', 'range', 'multi-range'],
        events: ['change', 'wb-calendar:change', 'input'],
        features: ['time-picker', 'action-button', 'block-past', 'json-config'],
        initialized: true
    };

    console.log('[Website Builder] Calendar component registered successfully');
});

// Fallback registration if Alpine is already loaded
if (typeof Alpine !== 'undefined') {
    console.log('[Website Builder] Alpine already loaded, registering calendar component (fallback)');
    Alpine.data('wbCalendarComponent', wbCalendarComponent);
    console.log('[Website Builder] Calendar component registered (fallback)');
}

// Debug: Check if the function is defined
console.log('[Website Builder] wbCalendarComponent function defined:', typeof wbCalendarComponent);
