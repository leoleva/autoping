const modelHtml = "<div class=\"col-sm-12 add-block\">\n" +
    "                            <div class=\"card border-light mb-3\">\n" +
    "                                <div class=\"card-header\">Pridėjimas</div>\n" +
    "                                <div class=\"card-body\">\n" +
    "                                    <div class=\"form-group\">\n" +
    "                                        <div class=\"select-image\">\n" +
    "                                            <label for=\"firstName\" class=\"form-label\">Pridėti nuotrauką</label>\n" +
    "                                            <input type=\"file\" class=\"form-control\" placeholder=\"\" value=\"\" required name=\"file[0]\">\n" +
    "                                        </div>\n" +
    "                                        <div class=\"image-selected text-center d-none\">\n" +
    "                                            <img src=\"\" style=\"max-height: 30em\" class=\"img-fluid rounded mx-auto d-block\" alt=\"...\">\n" +
    "                                            <button type=\"button\" class=\"remove-photo btn btn-super-sm btn-danger mt-1\">Pašalinti nuotrauką</button>\n" +
    "                                        </div>\n" +
    "                                        <label for=\"aprasymas\" class=\"form-label mt-1\">Aprašymas</label>\n" +
    "                                        <textarea class=\"form-control\" id=\"aprasymas\" rows=\"3\" name=\"text[0]\" required></textarea>\n" +
    "                                    </div>\n" +
    "                                </div>\n" +
    "                                <div class=\"card-footer d-flex align-items-lg-end\">\n" +
    "                                    <button type=\"button\" class=\"btn btn-danger remove-block\" style=\"margin-left: auto;\">Pašalinti</button>\n" +
    "                                </div>\n" +
    "                            </div>\n" +
    "                        </div>";

let i = 1;

$(function () {
    $(document).on('change', "input[type='file']", function (){
        handleImageSelection(this);
    });
    $(document).on('click', ".remove-job-photo", function (){
        let formGroup = $(this).parent().parent();

        let photoId = formGroup.find("input[type='hidden']").val();

        let request = $.ajax({
            url: "/job-photo/"+ photoId +"/delete",
            method: "POST",
            success: function () {
                formGroup.remove();
            }
        });
    });
    $(document).on('click', ".add-next-column", function () {
       let row = $(this).parent().parent().parent().parent();

       let lastAddBlock = $('.add-block');
       let i = lastAddBlock.last().length !== 0 ? lastAddBlock.last().find('textarea').attr('name').replace ( /[^\d.]/g, '' ).replace ( /[^\d.]/g, '' ) : 0;
       let model = $(modelHtml);

       i++;

       model.find("input[type='file']").attr('name', 'file['+i+']');
       model.find("textarea").attr('name', 'text['+i+']');

       if (lastAddBlock.last().length !== 0) {
           $(model).insertAfter(lastAddBlock.last());
       } else {
           $('.row').prepend(model);
       }
    });
    $(document).on('click', ".remove-block", function () {
        let addBlock = $(this).parent().parent().parent();

        addBlock.remove();
    });
});

function handleImageSelection(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            let formGroup = $(input).parent().parent();

            formGroup.find('.image-selected img').attr('src', e.target.result);
            formGroup.find('.image-selected').removeClass('d-none');
            formGroup.find('.select-image').addClass('d-none');
        }

        reader.readAsDataURL(input.files[0]);
    }
}
