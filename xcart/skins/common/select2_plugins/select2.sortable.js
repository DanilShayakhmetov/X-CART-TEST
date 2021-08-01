/**
 * jQuery Select2 Sortable
 * - enable select2 to be sortable via normal select element
 *
 * author      : Vafour
 * inspired by : jQuery Chosen Sortable (https://github.com/mrhenry/jquery-chosen-sortable)
 * License     : GPL
 */

(function($){
  $.fn.extend({
    select2Sortable: function(stopCallback){
      $this        = this.filter('[multiple]');

      var ul = $this.next('.select2-container').first('ul.select2-selection__rendered');
      ul.sortable({
        items: 'li:not(.select2-search)',
        cursor: "move",
        tolerance: 'pointer',
        stop: stopCallback
      });
    },
  });
}(jQuery));
