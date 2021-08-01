/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Extensions menu node
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function PopupButtonHotAddons() {
  PopupButtonHotAddons.superclass.constructor.apply(this, arguments);
}

extend(PopupButtonHotAddons, PopupButton);

PopupButtonHotAddons.prototype.pattern = '.action-widget .extensions';

core.autoload(PopupButtonHotAddons);
