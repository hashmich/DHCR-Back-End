

'use strict';



class Accordeon {

    constructor(identifier) {
        this.element = document.getElementById(identifier);
        this.handles = $(this.element).find(".accordeon-item > h3");
        this.addHandlers();
    }

    addHandlers() {
        for(let i = 0; this.handles.length > i; i++) {
            $(this.handles[i]).click(function() {
                this.clickHandler(this.handles[i]);
            }.bind(this));
        }
    }

    closeAll() {
        $('.accordeon-item.open').removeClass('open');
    }

    clickHandler(handle) {
        let item = $(handle).closest('.accordeon-item');
        //let wasOpen = (item.hasClass('open')) ? true : false;
        //this.closeAll();
        //if(!wasOpen) this.openItem(handle);
        this.toggleItem(handle);
    }

    openItem(handle) {
        let item = $(handle).closest('.accordeon-item');
        item.addClass('open');
    }

    closeItem(handle) {
        $(handle).closest('.accordeon-item').removeClass('open');
    }

    toggleItem(handle) {
        if($(handle).closest('.accordeon-item').hasClass('open')) {
            this.closeItem(handle);
        }else{
            this.openItem(handle);
        }
    }
}
