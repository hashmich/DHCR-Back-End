
'use strict';

class StartDate {

    constructor(input) {
        this.input = input;
        this.value = input.val();
        this.dates = [];
        this.recurring = $('#CourseRecurring').val();
        this.createModal();
    }

    closeHandler() {
        this.input.val(this.value);
    }

    createModal() {
        this.modal = new Modal('Set Start Date(s)', 'start-date', this.closeHandler.bind(this));

        this.modal.create();
    }

    static months(i) {
        let months = ['Jan','Feb','Mar','Apr','May','June','July','Aug','Sept','Oct','Nov','Dec'];
        if(typeof i != "undefined") {
            if(i >= 0 && i <= 11) return months[i];
        }else{
            return false;
        }
        return months;
    }

    static years() {
        let year = new Date().getFullYear();
        let years = [];
        for(let i = 0; i < 5; i++) {
            years.push(year + i);
        }
        return years;
    }

    createSelector(category) {
        let choose, options;
        if(category == 'month') {
            choose = 'Choose Month';
            options = StartDate.months();
        }
        if(category == 'year') {
            choose = 'Choose Year';
            options = StartDate.years();
        }
        if(category == 'day') {
            choose = 'Choose Day';
            options = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];
        }

        let wrapper = $('<div></div>').addClass('selector-wrapper');
        let styler = $('<div></div>').addClass('styled-select');
        wrapper.append(styler);
        let select = $('<select></select>').addClass('filter');
        select.append('<option selected disabled hidden>' + choose + '</option>');


        for (let i = 0; i < options.length; i++) {
            let option = this.createSelectOption(category, options, i);
            select.append(option);
        }

        styler.append(select);    // first child is always an empty option "choose..."

        return wrapper[0].outerHTML;
    }

    createSelectOption(category, list, i) {
        // test if category id is a valid option - raw options may not be in sync with filter state
        if(typeof list[i] != 'undefined') {

            let label = this.filter[category][id].name + ' (' + this.filter[category][id].course_count + ')';
            let option = $('<option></option>').text(list[i])
                .attr('data-category', category).attr('data-id', i);
            return option
        }
        return false;
    }

    createSelection(category) {
        if(Object.keys(this.filter.selected[category]).length > 0) {
            //let selection = $('<ul></ul>').addClass('selection').attr('id', category + '-selection');
            for(let id in this.filter.selected[category]) {
                let item = $('<div></div>')
                    .addClass('selection-item')
                    .attr('data-category', category).attr('data-id', id)
                    .text(this.filter.selected[category][id].name);
                selection.append(item);
            }
            return selection[0].outerHTML;
        }
        return '';
    }
}