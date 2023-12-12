$(function () {
    // editable
    $('.field_editable').editable({
        params: function (params) {
            params._token = $(this).attr('rel');
            var msg = $(this).editable().data('msg');
            if(typeof msg !== 'undefined') {
                params.msg = msg;
            }
            return params;
        },
        ajaxOptions: {
            success: function (data) {
              swal({
                  title:'Congrats',
                  text: data,
                },
              function(){
                  window.location.reload();
              });
            }
        }
    });
    //date picker
    $(".js_datepicker").datepicker({
        dateFormat: "dd-mm-yy"
//        onSelect: function() {
//            window.location = '/sales_report/' + encodeURI($(this).val());
//        }
    });
    //profile editable
    $('.profile_editable').editable({
        params: function (params) {
            params._token = $(this).attr('rel');
            return params;
        },
        validate: function(value) {
          var country = $(this).attr('data-country');
          if(country == '' || country == 'BD') {
            var found = value.match(/^(?:8801)[5-9](?:\d{8})$/i);
            if (!found) {
                return 'Invalid phone number.';
            }
          }

            if(($(this).data('name') == 'company_name') || ($(this).data('name') == 'company_address')) {
                if($.trim(value) == '') {
                    return 'This field is required';
                }
            }

        },
        ajaxOptions: {
            success: function (data) {
                swal({
                        title:'Congrats',
                        text: data,
                    },
                    function(){
                        window.location.assign("/profile");
                    });
            }
        }
    });

    $('.alpha-numeric').on('keyup', function (e) {

        var regex = new RegExp("^[a-zA-Z0-9- ]+$");
        // var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        var str = $(this).val();
        
        if (regex.test(str)) {
            return true;
        }

        if(str) {
            swal("Only Alpha Numeric \(a-z  0-9\) Allowed");
            e.preventDefault();
            
            $(this).val('');
            
            return false;
        }
    });
    
    $('.without-underscore-alpha-numeric').on('keyup', function (e) {

        var regex = new RegExp("^[a-zA-Z0-9_ ]+$");
        // var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        var str = $(this).val();
        
        if (regex.test(str)) {
            return true;
        }

        if(str) {
            swal("Only Alpha Numeric \(a-z  0-9_\) Allowed");
            e.preventDefault();
            
            $(this).val('');
            
            return false;
        }
    });
    
});
//printable area-------------------------
function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;
}

//---------------------start search into the page----------------------------
jQuery.fn.highlight = function (pat) {
    function innerHighlight(node, pat) {
        var skip = 0;
        if (node.nodeType == 3) {
            var pos = node.data.toUpperCase().indexOf(pat);
            if (pos >= 0) {
                var spannode = document.createElement('span');
                spannode.className = 'highlight';
                var middlebit = node.splitText(pos);
                var endbit = middlebit.splitText(pat.length);
                var middleclone = middlebit.cloneNode(true);
                spannode.appendChild(middleclone);
                middlebit.parentNode.replaceChild(spannode, middlebit);
                skip = 1;
            }
        }
        else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
            for (var i = 0; i < node.childNodes.length; ++i) {
                i += innerHighlight(node.childNodes[i], pat);
            }
        }
        return skip;
    }
    return this.each(function () {
        innerHighlight(this, pat.toUpperCase());
    });
};

jQuery.fn.removeHighlight = function () {
    function newNormalize(node) {
        for (var i = 0, children = node.childNodes, nodeCount = children.length; i < nodeCount; i++) {
            var child = children[i];
            if (child.nodeType == 1) {
                newNormalize(child);
                continue;
            }
            if (child.nodeType != 3) {
                continue;
            }
            var next = child.nextSibling;
            if (next == null || next.nodeType != 3) {
                continue;
            }
            var combined_text = child.nodeValue + next.nodeValue;
            new_node = node.ownerDocument.createTextNode(combined_text);
            node.insertBefore(new_node, child);
            node.removeChild(child);
            node.removeChild(next);
            i--;
            nodeCount--;
        }
    }

    return this.find("span.highlight").each(function () {
        var thisParent = this.parentNode;
        thisParent.replaceChild(this.firstChild, this);
        newNormalize(thisParent);
    }).end();
};
$(function () {
    $('#text-search').bind('keyup change', function (ev) {
        // pull in the new value
        var searchTerm = $(this).val();

        // remove any old highlighted terms
        $('body').removeHighlight();

        // disable highlighting if empty
        if (searchTerm) {
            // highlight the new term
            $('body').highlight(searchTerm);
        }
    });
});
//-------------------end search into the page------------------------

//back to top

jQuery(document).ready(function($){
    // browser window scroll (in pixels) after which the "back to top" link is shown
    var offset = 300,
    //browser window scroll (in pixels) after which the "back to top" link opacity is reduced
        offset_opacity = 1200,
    //duration of the top scrolling animation (in ms)
        scroll_top_duration = 700,
    //grab the "back to top" link
        $back_to_top = $('.cd-top');

    //hide or show the "back to top" link
    $(window).scroll(function(){
        ( $(this).scrollTop() > offset ) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
        if( $(this).scrollTop() > offset_opacity ) {
            $back_to_top.addClass('cd-fade-out');
        }
    });

    //smooth scroll to top
    $back_to_top.on('click', function(event){
        event.preventDefault();
        $('body,html').animate({
                scrollTop: 0 ,
            }, scroll_top_duration
        );
    });

});

$(function() {
    $("form").submit(function() {
        $(this).submit(function() {
            return false;
        });
        return true;
    });
});

$(function(){
    $('.delete-swl').click(function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        var that = this;

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false

        }, function(result){
            if(result){
                if(typeof href !== 'undefined'){
                    window.location.href = href;
                } else {
                    $(that).closest("form").submit();
                }
            }
        });
    });
});
