
'use strict';

class StartDate {

    constructor(target) {
        this.dates = $('#CourseStartDate').val().split(';').filter(function(v, i, arr) {
            v = v.trim();
            return StartDate.validate(v);
        });
        // cancelling the click on checkbox is weird
        if(target.attr('id') == 'CourseRecurring') {
            this.recurring = !$('#CourseRecurring').is(':checked');
            $('#CourseRecurring').prop('checked', this.recurring);
        }
        this.createModal();
        this.addHandlers();
    }

    addHandlers() {
        $('#modal-wrapper').on('change', '.datepicker', function(e) {
            let input = $(e.target);
            this.dates[input.attr('data-index')] = input.val();
            this.createResult();
        }.bind(this));

        $('#modal-wrapper').on('click', '.add-date', function(e) {
            this.addDate($('#dateContainer'), this.dates.length);
        }.bind(this));

        $('#modal-wrapper').on('click', '.delete-date', function(e) {
            this.removeDate($(e.target).attr('data-index'));
            this.createResult();
        }.bind(this));

        $('#modal-wrapper').on('click', '.radio-selector.recurring li.option:not(.selected)', function(e) {
            let selector = $(e.target).closest('.radio-selector');
            let target = $(e.target).closest('li.option');
            let filterKey = selector.attr('data-filter-key');
            selector.find('li.selected').removeClass('selected');
            target.addClass('selected');
            this.recurring = StartDate.parseValue(target.attr('data-value'));
            this.createResult();
        }.bind(this));
    }

    picker(jq) {
        jq.datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            showAnim: 'slideDown',
            minDate: new Date(new Date().getFullYear() + '-01-01'),
            maxDate: '+5Y',
            dateFormat: "yy-mm-dd",         // results in YYYY-mm-dd
            gotoCurrent: true,
            hideIfNoPrevNext: true      // trigger externally

        });
    }

    addDate(container, index) {
        container.append($('<label>Date</label>').attr('data-index', index));
        container.append(this.createDateInput(index));
        container.append($('<span class="delete-date">delete</span>').attr('data-index', index));
    }

    removeDate(index) {
        this.dates.splice(index, 1);
        $('#dateContainer').replaceWith(this.createDateContainer());
    }

    createResult() {
        let readable = [];
        for(let i = 0; i < this.dates.length; i++) {
            let date = new Date(this.dates[i]);
            let str = StartDate.months(date.getMonth()) + ' ' + date.getDate();
            if(!this.recurring) str += ', ' + date.getFullYear();
            readable.push(str);
        }
        let separator = ' <span class="separator">•••</span> ';
        if($('#StartDateResult').length) $('#StartDateResult').html(readable.join(separator));
        else return '<p id="StartDateResult">' + readable.join(separator) + '</p>';
    }

    onClose() {
        $('#CourseStartDate').val(this.dates.join(';'));
        $('#CourseRecurring').prop('checked', this.recurring);
    }

    createModal() {
        this.modal = new Modal('Course Start Date(s)', 'start_date', this.onClose.bind(this));
        this.modal.add(this.createResult());
        this.modal.add(this.createDateContainer());
        this.modal.add('<p><span class="add-date">Add Date</span></p>');
        this.modal.add(this.createOccurrenceSelector());
        this.modal.create();
    }

    createDateContainer() {
        let container = $('<div></div>').attr('id', 'dateContainer');
        if(this.dates.length > 0) {
            for(let i = 0; i < this.dates.length; i++) {
                this.addDate(container, i);
            }
        }else{
            // add empty input if dates is empty
            container.append($('<label>Date</label>').attr('data-index', 0));
            container.append(this.createDateInput());
            container.append($('<span class="delete-date">Delete</span>').attr('data-index', 0));
        }
        return container;
    }

    createDateInput(index) {
        index = index || 0;
        let input = $('<input>').addClass('datepicker')
            .attr('data-index', index);
        if(typeof this.dates[index] !== 'undefined') {
            input.val(this.dates[index].trim());
        }
        this.picker(input);
        return input;
    }

    createOccurrenceSelector() {
        let options = [
            {label: 'yes', value: true},
            {label: 'no', value: false}
        ];
        return this.createRadioSelector('recurring', 'recurring',
            'Recurring', options, $('#CourseRecurring').is(':checked'));
    }

    createRadioSelector(classname, id, label, options, value) {
        classname = (classname !== false)
            ? (typeof classname == 'string' && classname.length > 0)
                ? classname + ' radio-selector' : 'radio-selector'
            : '';

        let list = $('<ul></ul>').attr('id', id).addClass(classname);
        list.append($('<li></li>').addClass('label').html(label));
        for(let i = 0; i < options.length; i++) {
            if(typeof value === 'undefined' || value == null || !value) value = false;
            let option = $('<li></li>').html(options[i].label)
                .attr('data-value', options[i].value)
                .addClass('option');
            if(options[i].value == value)
                option.addClass('selected');

            list.append(option);
        }
        return list;
    }

    static validate(date) {
        return date.match(/^2\d{3}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/);
    }

    static months(i) {
        let months = ['Jan','Feb','Mar','Apr','May','June','July','Aug','Sept','Oct','Nov','Dec'];
        if(typeof i != "undefined" && i >= 0 && i <= 11) return months[i];
        return months;
    }

    static parseValue(value) {
        if(value == 'null') value = null;
        else if(value == 'false') value = false;
        else if(value == 'true') value = true;
        return value;
    }
}