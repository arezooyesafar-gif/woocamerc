function woosbymdz_admin_pro_check_charge(btn){

    var is_sharjed = btn.hasAttribute("checked");
    if(is_sharjed){
        is_sharjed = 1;
        btn.removeAttribute("checked");
    }else {
        is_sharjed = 0;
        btn.setAttribute("checked", "");
    }

    /*Ajax*/
    var con = btn.parentNode.parentNode;
    var pid = btn.getAttribute("pid");
    jQuery.ajax({

        url: ajaxurl,
        method: 'post',
        cache: false,
        data: {
            action: 'woosbymdz_check_pro_charge',
            is_sharjed: is_sharjed,
            pid: pid,

            sedamej12: woosbymdz_chargeds_ajx_obj.sedamej12,//Security
        },
        beforeSend: function(){
            con.classList.add("woosbymdz-in-load");
        },
        error: function (err) {

            con.classList.remove("woosbymdz-in-load");
            console.info("Admin Ajax Error: " + err);

        },
        success: function (resp) {

            con.classList.remove("woosbymdz-in-load");
            console.log(resp);

        },
    });
    
}