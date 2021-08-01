/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * core.element.js
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
Element.prototype.fireEvent = Window.prototype.fireEvent = function fireEvent(event, eventInterface) {
  eventInterface = eventInterface || 'Event';
  emitEvent(this, event, eventInterface);
};

function emitEvent(object, event, eventInterface) {
  if (document.createEventObject) {
    // dispatch for IE
    var evt = document.createEventObject();
    return object.fireEvent('on' + event, evt);
  } else {
    // dispatch for firefox + others
    eventInterface = eventInterface || 'Event';
    var evt = document.createEvent(eventInterface);
    evt.initEvent(event, true, true); // event type,bubbling,cancelable
    return !object.dispatchEvent(evt);
  }
}