'use strict';

/*
 * Begin of functions
 */

// Insert text into textarea at Caret Position
function insert_at_caret(element, text) {
  element = $(element);
  var caretPos = element[0].selectionStart,
    currentValue = element.val();

  element.val(currentValue.substring(0, caretPos) + text + currentValue.substring(caretPos));
}

function update_email_template_preview() {
  $('#email-template-preview').contents().find('body').html($('.email-template-body').val());
}

// Insert HTML tags into textarea
function insert_html_tag(tag_type, destination_id) {
  var text, sel, text_area, selectedText, startPos, endPos, replace, replaceText, len;
  switch (tag_type) {
    case 'text-bold':
      text = ['<b>', '</b>'];
      break;
    case 'text-italic':
      text = ['<em>', '</em>'];
      break;
    case 'text-paragraph':
      text = ['<p>', '</p>'];
      break;

    case 'text-h1':
      text = ['<h1>', '</h1>'];
      break;
    case 'text-h2':
      text = ['<h2>', '</h2>'];
      break;
    case 'text-h3':
      text = ['<h3>', '</h3>'];
      break;
    case 'text-h4':
      text = ['<h4>', '</h4>'];
      break;

    case 'text-code':
      text = ['<code>', '</code>'];
      break;
    case 'text-hr':
      text = ['<hr/>', ''];
      break;
    case 'text-css':
      text = ['<style></style>', ''];
      break;
  }

  // Get the selected text
  text_area = document.getElementById(destination_id);
  if (document.selection !== undefined) {
    text_area.focus();
    sel = document.selection.createRange();
    selectedText = sel.text;
  } else if (text_area.selectionStart !== '') {
    startPos = text_area.selectionStart;
    endPos = text_area.selectionEnd;
    selectedText = text_area.value.substring(startPos, endPos);
  }

  // Check if <style> should be added
  if (tag_type === 'text-css') {
    replace = text[0] + '\n\r' + text_area.value;
    $(text_area).val(replace);
    update_email_template_preview();
    return true;
  }

  // Check if there is only one HTML tag
  if (text[1].length === 0) {
    insert_at_caret(destination_id, text[0]);
    update_email_template_preview();
    return true;
  }

  // Check if text is selected, replace it or just insert the tag at cursor position
  if (!selectedText || !selectedText.length) {
    text = text[0] + text[1];
    insert_at_caret(destination_id, text);
    update_email_template_preview();
  } else {
    replaceText = text[0] + selectedText + text[1];
    len = text_area.value.length;
    replace = text_area.value.substring(0, startPos) + replaceText + text_area.value.substring(endPos, len);
    $(text_area).val(replace);
    update_email_template_preview();
  }
}

/*
 * Notes handling
 */
function init_notes(siteUrl) {
  // Save note form handling
  $('#save-note').click(function (button) {
    button.preventDefault();

    // Prepare vars
    var url = siteUrl + '/notes/notes_ajax/save_note',
      type = $(this).data('type'),
      type_id = $(this).data('id');

    show_loader();

    // Fire the ajax request
    $.ajax({
      method: 'post',
      url: url,
      data: {
        type: type,
        type_id: type_id,
        note: $('#note-content').val()
      }
    }).done(function (data) {
      var response = JSON.parse(data);

      hide_loader();

      if (response.success === true) {
        // The validation was successful
        $(button).parents('.form-group').removeClass('has-danger');

        window.setTimeout(function () {
          // Clear the textarea and update the notes
          $('#note-content').val('');
          reload_notes(siteUrl, type, type_id);
        }, 500);
      } else {
        // The validation was not successful
        $(button).addClass('has-danger');

        for (var key in response.validation_errors) {
          $('#' + key).parents('.input-group').addClass('has-danger');
        }
      }
    });
  });

  $('.delete-note').click(function (button) {
    button.preventDefault();

    // Prepare the vars
    var url = siteUrl + '/notes/notes_ajax/delete_note',
      save_button = $('#save-note'),
      type = save_button.data('type'),
      type_id = save_button.data('id');

    show_loader();

    // Fire the ajax request
    $.ajax({
      method: 'post',
      url: url,
      data: {
        user_id: $(this).data('user-id'),
        note_id: $(this).data('note-id')
      }
    }).done(function (data) {
      var response = JSON.parse(data);

      hide_loader();

      if (response.success === true) {
        // The validation was successful
        window.setTimeout(function () {
          // Update the notes
          reload_notes(siteUrl, type, type_id);
        }, 500);
      }
    });
  });
}

// Function to reload the notes by type and type id
function reload_notes(siteUrl, type, type_id) {
  var url = siteUrl + '/notes/notes_ajax/get_notes';

  $.ajax({
    method: 'post',
    url: url,
    data: {
      type: type,
      type_id: type_id
    }
  }).done(function (data) {
    $('.notes-content').html(data);
    // Reinitialize the notes
    init_notes(siteUrl);
  });
}

/*
 * Loader handling
 */
function show_loader() {
  $('#loader').slideDown(200);
}

function hide_loader() {
  $('#loader').delay(1000).slideUp(200);
}

// Delay function that only continues after a predefined time
var delay = (function () {
  var timer = 0;
  return function (callback, ms) {
    clearTimeout(timer);
    timer = setTimeout(callback, ms);
  };
})();

/*
 |   =========================================================
 |   Begin of the scripts fired afer document is ready
 |   =========================================================
 */
$(document).ready(function () {

  // Global variables
  var siteUrl = $('#site-url').text();

  /*
   * Height calculation
   */
  var doc = $(document);
  var docHeight = doc.height();
  var main = $('#main');
  var content = $('#content');
  var mainHeight = docHeight - $('.sidebar-toggle-wrapper').outerHeight();
  var contentHeight = mainHeight - $('#headerbar').outerHeight();

  if (main.height() < docHeight) {
    main.outerHeight(mainHeight);
  }

  if (content.height() < docHeight) {
    content.outerHeight(contentHeight);
  }

  /*
   * Dropdown handling
   * Dropdowns open above the button if below 2/3 of the page to prevent additional scrolling
   */
  var docFold = (docHeight / 3) * 2;
  $('[data-toggle="dropdown"]').each(function () {
    var toggle = $(this);
    if (toggle.offset().top > docFold) {
      toggle.parent().find('.dropdown-menu').css('top', 'auto').css('bottom', toggle.outerHeight());
    }
  });

  /*
   * Tooltip initialization
   */
  $('[data-toggle="tooltip"]').tooltip();

  /*
   * Loader initialization
   */
  $('*[type="submit"], .show-loader').bind('click', function () {
    $('#loader').slideDown(200);
    $('#loader-error').delay(10000).fadeIn(200);
    $('#loader-indicator').delay(10000).slideUp(200);
  });

  /*
   * Sidebar toggle handling
   */
  $('.sidebar-toggle').click(function (e) {
    e.preventDefault();
    $('#sidebar').toggleClass('show-sidebar');
  });

  // Note initialization
  init_notes(siteUrl);

  /*
   * Set the height of an element to 100%
   */
  $('.match-parent-height').each(function () {
    $(this).height($(this).parent().height());
  });

  /**
   * Template tag handling
   * Example tag
   * <a href="#" data-target="#input_field" data-tag="{{{client_name}}}">Client Name</a>
   */
  $('[data-tag]').bind('click', function () {
    insert_at_caret($(this).data('target'), $(this).data('tag'));
    return false;
  });

  /*
   * Email Template form handling
   * Handle click event for Email Template Tags insertion
   * Example Usage
   * <a href='#' class='text-tag' data-tag='{{{client_name}}}'>Client Name</a>
   *
   * @TODO Needs refactoring!
   */
  if ($('#email-template')) {

    // Keep track of the last 'taggable' input/textarea
    $('.taggable').on('focus', function () {
      // var lastTaggableClicked = this;
    });

    // HTML tags to email templates textarea
    $('.html-tag').click(function () {
      var tag_type = $(this).data('tagType');
      var body_id = $('.email-template-body').attr('id');
      insert_html_tag(tag_type, body_id);
    });

    // Email Template Preview handling
    var email_template_body_id = $('.email-template-body').attr('id');

    if ($('#email_template_preview').empty()) {
      update_email_template_preview();
    }

    $(email_template_body_id).bind('input propertychange', function () {
      delay(function () {
        update_email_template_preview();
      }, 200);
    });

    $('#email-template-preview-reload').click(function () {
      update_email_template_preview();
    });

  }
});
