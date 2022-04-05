const modelHtml = "                        <div class=\"col-sm-12 add-block\">\n" +
    "                            <div class=\"card border-light mb-3\">\n" +
    "                                <div class=\"card-header\">Pridėjimas</div>\n" +
    "                                <div class=\"card-body\">\n" +
    "                                    <div class=\"form-group\">\n" +
    "                                        <div class=\"select-image\">\n" +
    "                                            <label for=\"firstName\" class=\"form-label\">Pridėti nuotrauką</label>\n" +
    "                                            <input type=\"file\" class=\"form-control\" placeholder=\"\" value=\"\" required name=\"title\">\n" +
    "                                        </div>\n" +
    "                                        <div class=\"image-selected text-center d-none\">\n" +
    "                                            <img src=\"https://images.hindustantimes.com/img/2021/06/05/550x309/furry_doggo_1622886675678_1622886686897.PNG\" style=\"max-height: 30em\" class=\"img-fluid rounded mx-auto d-block\" alt=\"...\">\n" +
    "                                            <button type=\"button\" class=\"remove-photo btn btn-super-sm btn-danger mt-1\">Pašalinti nuotrauką</button>\n" +
    "                                        </div>\n" +
    "                                        <label for=\"aprasymas\" class=\"form-label mt-1\">Aprašymas</label>\n" +
    "                                        <textarea class=\"form-control\" id=\"aprasymas\" rows=\"3\" required></textarea>\n" +
    "                                    </div>\n" +
    "                                </div>\n" +
    "                                <div class=\"card-footer d-flex align-items-lg-end\">\n" +
    "                                    <button type=\"button\" class=\"btn btn-danger remove-block\" style=\"margin-left: auto;\">Pašalinti</button>\n" +
    "                                </div>\n" +
    "                            </div>\n" +
    "                        </div>";

$(function () {
    $(document).on('change', "input[type='file']", function (){
        handleImageSelection(this);
    });
    $(document).on('click', ".remove-photo", function (){
        let formGroup = $(this).parent().parent();

        formGroup.find('.select-image').removeClass('d-none');
        formGroup.find('.image-selected').addClass('d-none');
        formGroup.find("input[type='file']").val('');
    });
    $(document).on('click', ".add-next-column", function () {
       let row = $(this).parent().parent().parent().parent();

       let lastAddBlock = $('.add-block');

       if (lastAddBlock.last().length !== 0) {
           $(modelHtml).insertAfter(lastAddBlock.last());
       } else {
           $('.row').prepend(modelHtml);
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
