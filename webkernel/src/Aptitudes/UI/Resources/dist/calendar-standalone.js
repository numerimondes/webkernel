// Standalone Calendar Component - Ready to use without compilation
// No imports, no exports, just pure JavaScript

// Matcher class
class Matcher {
    constructor(rule) {
        this.type = this.determineMatcherTypesss(rule)
        this.rule = rule;
    }

    passes(date) {
        date = this.createDateWithoutTime(date)
        if (this.type == 'dates') {
            return this.rule.dates.some(element =>  date.getTime() == this.createDateWithoutTime(element).getTime());
        } else if (this.type == 'range') {
            if (this.rule.before != null && date.getTime() < this.createDateWithoutTime(this.rule.before).getTime()) {
                return true
            }
            if (this.rule.after != null && date.getTime() > this.createDateWithoutTime(this.rule.after).getTime()) {
                return true
            }
            return false
        } else if (this.type == 'dayOfWeek') {
            if (typeof this.rule.dayOfWeek == 'number') {
               return date.getDay() == this.rule.dayOfWeek
            }else{
                return this.rule.dayOfWeek.some(rule => rule == date.getDay())
            }
        }
        return false
    }

    determineMatcherType(rule) {
        if (rule.dates != undefined && Array.isArray(rule.dates)) {
            return "dates"
        } else if (rule.before != undefined || rule.after != undefined) {
            return "range"
        } else if (rule.dayOfWeek != undefined) {
            return "dayOfWeek"
        }
    }

    createDateWithoutTime(value) {
        let date = new Date(value)
        date.setHours(0,0,0,0);
        return date;
    }
}

// SingleModeHandler class
class SingleModeHandler {
    constructor(selected, required) {
        this.required = !!required;
        this.dayClicked(this.createDateWithoutTime(selected))
    }

    get value() {
        return this._value
    }

    set value(value) {
        const processDate = (input) => {
            if (input == null) return null;
            if (typeof input === "string") return this.createDateWithoutTime(input);
            if (input instanceof Date) return input;
            console.warn("Item is not a date or date string, skipping");
            return null;
        };
        this._value = processDate(value)
    }

    dayClicked(date) {
        if (this._value != null && this._value.getTime() == date.getTime() && !this.required) {
            this._value = null
        } else {
            this._value = date
        }
        return true
    }

    isSelectedDay(date) {
        return this._value?.getTime() === date.getTime();
    }

    isDisabled(date) {
        return false;
    }

    createDateWithoutTime(value) {
        let date = new Date(value)
        date.setHours(0, 0, 0, 0);
        return date;
    }
}

// MultipleModeHandler class
class MultipleModeHandler {
    constructor(selected, required, min, max) {
        this.min = min
        this.max = max
        this._value = [];
        this.required = !!required;

        if (selected && Array.isArray(selected)) {
            selected.forEach(element => {
                this.dayClicked(this.createDateWithoutTime(element))
            });
        }
    }

    get value() {
        return this._value
    }

    set value(value) {
        if (!Array.isArray(value)) {
            console.warn('Selected type supplied to calendar in multiple mode is not an array')
            return
        }
        value.forEach(item => {
            const processDate = (input) => {
                if (input == null) return null;
                if (typeof input === "string") return this.createDateWithoutTime(input);
                if (input instanceof Date) return input;
                console.warn("Item is not a date or date string, skipping");
                return null;
            };
            item = processDate(item)
            if (this.isSelectedDay(item)) {
                return;
            }
            this._value.push(item)
        });
    }

    isDisabled(date) {
        if (this.max && this.max <= this._value.length) {
            return !this.isSelectedDay(date)
        }
    }

    indexOfDateInValue(array, value) {
        for (let index = 0; index < array.length; index++) {
            const date = array[index];
            if (date.getTime() === value.getTime()) {
                return index;
            }
        }
        return -1;
    }

    dayClicked(date) {
        let index = this.indexOfDateInValue(this._value, date)
        if (index >= 0) {
            this._value.splice(index, 1);
        } else {
            this._value.push(date)
        }
        return true;
    }

    isSelectedDay(date) {
        return this.indexOfDateInValue(this._value, date) >= 0;
    }

    createDateWithoutTime(value) {
        let date = new Date(value)
        date.setHours(0, 0, 0, 0);
        return date;
    }
}

// RangeModeHandler class
class RangeModeHandler {
    constructor(selected, required, min, max) {
        this.min = min
        this.max = max
        this.required = !!required;
        this._value = { 'from': null, 'to': null }

        if (selected.from) {
            this.dayClicked(this.createDateWithoutTime(selected.from))
        }
        if (selected.from && selected.to) {
            this.dayClicked(this.createDateWithoutTime(selected.to))
        }
    }

    dayClicked(date) {
        if (this._value.from == null || (this._value.to != null && this._value.to.getTime() == date.getTime())) {
            this._value.from = date
            this._value.to = null
            return true;
        }

        if (this._value.from.getTime() == date.getTime()) {
            this._value.from = this.required ? this._value.from : null
            this._value.to = null
            return true;
        }

        if (this._value.from.getTime() >= date.getTime()) {
            this._value.from = date
            return true;
        }

        this._value.to = date
        return true;
    }

    isSelectedDay(date) {
        if (this._value.from == null) {
            return false;
        }

        if (this._value.to == null) {
            return this._value.from.getTime() == date.getTime()
        }

        return date.getTime() == this._value.from.getTime() || date.getTime() == this._value.to.getTime()
    }

    get value() {
        return this._value
    }

    set value(value) {
        if (this._value == null) {
            this._value = { from: null, to: null };
        }

        if (value == null) {
            return;
        }

        const processDate = (input) => {
            if (input == null) return null;
            if (typeof input === "string") return this.createDateWithoutTime(input);
            if (input instanceof Date) return input;
            console.warn("Item is not a date or date string, skipping");
            return null;
        };

        this._value.from = processDate(value.from);
        this._value.to = processDate(value.to);
    }

    isDisabled(date) {
        if (this._value.from) {
            let daysBetween = Math.abs(this.getNumberOfDaysBetweenDates(this._value.from, date))
            return (((this.min && daysBetween < this.min) || (this.max && daysBetween > this.max)) && daysBetween != 0)
        }
    }

    isRangeMiddle(date) {
        if (this._value.from && this._value.to && date.getTime() >= this._value.from.getTime() && date.getTime() <= this._value.to.getTime()) {
            return true
        }
        return false
    }

    createDateWithoutTime(value) {
        let date = new Date(value)
        date.setHours(0, 0, 0, 0);
        return date;
    }

    getNumberOfDaysBetweenDates(date1, date2) {
        return Math.round((date1.getTime() - date2.getTime()) / (1000 * 3600 * 24));
    }
}

// Calendar function - ready to use with Alpine.js
function calendar(selected, mode, disabled, min, max, required) {
    return {
        focusedDay: '',
        mode: mode,
        max: max,
        min: min,
        month: '',
        year: '',
        daysInMonth: [],
        preBlankDaysInMonth: [],
        postBlankDaysInMonth: [],
        modeHandler: null,
        disabled: [],
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        root: {
            ['@keydown.left.prevent']() {
                this.focusAdd(-1)
            },
            ['@keydown.right.prevent']() {
                this.focusAdd(1)
            },
            ['@keydown.up.prevent']() {
                this.focusAdd(-this.days.length)
            },
            ['@keydown.down.prevent']() {
                this.focusAdd(this.days.length)
            },
            ['x-transition']() {
                return true;
            },
        },
        previousMonthTrigger: {
            ['@click']() {
                this.previousMonth()
            },
        },
        nextMonthTrigger: {
            ['@click']() {
                this.nextMonth()
            }
        },
        yearLabel: {
            ['x-text']() {
                return this.year
            }
        },
        monthLabel: {
            ['x-text']() {
                return this.monthNames[this.month]
            }
        },
        init() {
            if (this.mode == "single") {
                this.modeHandler = new SingleModeHandler(selected, required)
            } else if (this.mode == "multiple") {
                this.modeHandler = new MultipleModeHandler(selected, required, min, max)
            } else if (this.mode == "range") {
                this.modeHandler = new RangeModeHandler(selected, required, min, max)
            } else {
                console.error("Mode is invalid, defaulting to single mode")
                this.modeHandler = new SingleModeHandler(selected, required)
            }

            // add items to the disabled rules array
            if (Array.isArray(disabled)) {
                disabled.forEach((element) => {
                    this.disabled.push(new Matcher(element))
                });
            } else if (typeof disabled == 'object' && disabled != null) {
                this.disabled = [new Matcher(disabled)]
            }

            let now = new Date();
            this.month = now.getMonth();
            this.year = now.getFullYear();
            this.focusedDay = now.getDay();
            this.calculateDays();

            if (!!selected) {
                this.dispatchChange()
            }
        },
        dispatchChange() {
            this.$nextTick(() => { this.$dispatch('change', { value: this.modeHandler.value }) })
        },
        dayClicked(date) {
            let selectedDate = new Date(this.year, this.month, date);
            if (this.isDisabled(selectedDate)) {
                return;
            }
            this.focusedDay = date;
            let dispatchEvent = this.modeHandler.dayClicked(selectedDate)
            if (dispatchEvent) {
                this.dispatchChange()
            }
        },
        focusAdd(value) {
            if (!Number.isInteger(this.focusedDay)) {
                this.focusedDay = (new Date(this.year, this.month, day)).getDate();
            }
            this.focusedDay = this.focusedDay + value;
            if (this.focusedDay > this.daysInMonth.length) {
                this.focusedDay = this.focusedDay - this.daysInMonth.length;
                this.nextMonth();
            }
            else if (this.focusedDay <= 0) {
                this.previousMonth();
                this.focusedDay = this.daysInMonth.length + this.focusedDay
            }
        },
        previousMonth() {
            if (this.month == 0) {
                this.year--;
                this.month = 12;
            }
            this.month--;
            this.calculateDays();
        },
        nextMonth() {
            if (this.month == 11) {
                this.month = 0;
                this.year++;
            } else {
                this.month++;
            }
            this.calculateDays();
        },
        isSelectedDay(day) {
            let date = new Date(this.year, this.month, day)
            return this.modeHandler.isSelectedDay(date)
        },
        isFocusedDate(day) {
            return this.focusedDay === day ? true : false;
        },
        isToday(day) {
            const today = new Date();
            const d = new Date(this.year, this.month, day);
            return today.toDateString() === d.toDateString() ? true : false;
        },
        calculateDays() {
            let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
            let daysInPreviousMonth = new Date(this.year, this.month, 0).getDate();
            // find where to start day of week
            let dayOfWeek = new Date(this.year, this.month).getDay();
            let preBlankdaysArray = [];
            for (var i = 1; i <= dayOfWeek; i++) {
                preBlankdaysArray.push(daysInPreviousMonth - i + 1);
            }

            //if the length of the preblank arrays is a multiple of the week, it is considered an entire week
            preBlankdaysArray = preBlankdaysArray.reverse();
            let postBlankdaysArray = [];
            // always display 6 rows
            for (var i = 1; i <= (this.days.length * 6 - (preBlankdaysArray.length + daysInMonth)); i++) {
                postBlankdaysArray.push(i);
            }
            let daysArray = [];
            for (var i = 1; i <= daysInMonth; i++) {
                daysArray.push(i);
            }
            this.preBlankDaysInMonth = preBlankdaysArray;
            this.postBlankDaysInMonth = postBlankdaysArray;
            this.daysInMonth = daysArray;
        },
        isDisabled(date) {
            return this.disabled.some(rule => rule.passes(date)) || this.modeHandler.isDisabled(date);
        },
        isRangeMiddle(date) {
            if (mode == 'range') {
                return this.modeHandler.isRangeMiddle(date)
            }
            return false
        }
    }
}

// Register with Alpine.js when available
document.addEventListener('alpine:init', () => {
    Alpine.data('calendar', calendar);
});
