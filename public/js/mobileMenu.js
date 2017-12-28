
function toggleMenu() {

    if($("#mobileMenuBar").hasClass('hidden')) {
        $("#mobileMenuBar").removeClass('hidden');
        $(document.body).css('max-height', '100vh').css('overflow', 'hidden');
    }
    else {
        $("#mobileMenuBar").addClass('hidden');
        $(document.body).css('max-height', '').css('overflow', '');
    }
}