/*
*   This content is licensed according to the W3C Software License at
*   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
*
*   File:   Treeitem.js
*
*   Desc:   Setup click events for Tree widget examples
*/

/**
 * ARIA Treeview example
 * @function onload
 * @desc  after page has loaded initialize all treeitems based on the role=treeitem
 */

window.addEventListener('load', function () {

  var treeitems = document.querySelectorAll('[role="treeitem"]');

  for (var i = 0; i < treeitems.length; i++) {

    treeitems[i].addEventListener('click', function (event) {
      var treeitem = event.currentTarget;
      console.log('treeitem',treeitem);
      var parent_id = treeitem.getAttribute('data-parent_id');
      var parent_Name = treeitem.getAttribute('data-cate_name');
      document.getElementById('last_action').value = parent_id;
      document.getElementById('category_selected').value = parent_Name;
      event.stopPropagation();
      event.preventDefault();
    });

  }

});