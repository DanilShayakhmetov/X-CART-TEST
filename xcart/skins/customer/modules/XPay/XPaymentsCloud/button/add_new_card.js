/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Add new card button controller
 *
 */

var PopupButtonAddNewCard = PopupButton.extend({
    pattern: '.popup-button.add-new-card',
    enableBackgroundSubmit: true,
    constructor: function PopupButtonAddNewCard() {
        PopupButtonAddNewCard.superclass.constructor.apply(this, arguments);
    },
    callback: function(selector) {
        PopupButtonAddNewCard.superclass.callback.apply(this, arguments);

        var self = this;
        jQuery('form', selector).each(function() {
            jQuery(this).commonController(
              'enableBackgroundSubmit',
              _.bind(self.onBeforeSubmit, self),
              _.bind(self.onAfterSubmit, self)
            );
        });
    },
    beforeLoadDialog: function() {},
    onBeforeSubmit: function() {},
    onAfterSubmit: function() {
        popup.close();
    }
});

core.autoload(PopupButtonAddNewCard);
