$( document ).ready(function() {

    $(document).on('change', "#user_type", function (){

        if ($(this).val() == 'specialist') {
            $('#name_col').removeClass('d-none');
            $('#account_col').removeClass('d-none');

            $('#name_col').find('input').attr('required',  true);
            $('#account_col').find('input').attr('required',  true);
        } else {
            $('#name_col').addClass('d-none');
            $('#account_col').addClass('d-none');

            $('#name_col').find('input').removeAttr('required');
            $('#account_col').find('input').removeAttr('required');
        }
    });
});