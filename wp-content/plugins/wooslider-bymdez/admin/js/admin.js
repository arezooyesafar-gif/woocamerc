function woosbymdz_copycode(code) {
    navigator.clipboard.writeText(code).then(
        () => {
            alert(copy_trans1);
        },
        () => {
            alert(copy_trans2);
        }
    );
}
function woosbymdz_finpar(dom, key) {

    let par, last_par;

    par = dom.parentNode;

    if (par === null || par === undefined) {
        return false;
    }

    last_par = par;

    key = String(key);
    let find_mode = "tag";

    if (key.includes("#")) {

        find_mode = "id";
        key = key.split("#");
        key = key[1];

    } else if (/^\[.*]$/.test(key)) {

        find_mode = "attr";
        var regex = /\[(.*?)\]/g;
        var matches = key.match(regex);
        var results = matches.map(match => match.slice(1, -1));
        key = results[0];

    } else if (key.includes(".")) {

        find_mode = "class";
        key = key.split(".");
        key = key[1];
    }


    let this_attr;
    switch (find_mode) {
        case 'tag':
            key = key.toLowerCase();
            var tag_name = par.tagName.toLowerCase();
            while (par !== null && par !== undefined && tag_name != key) {
                last_par = par;
                par = par.parentNode;
            }
            break;
        case 'id':
            while (par !== null && par !== undefined && par.id != key) {
                last_par = par;
                par = par.parentNode;
            }
            break;
        case 'attr':
            if (key.includes("=")) {

                find_mode = "hard_attr";
                key = key.split("=")
                let has_attr = par.hasAttribute(key[0]);
                this_attr = '';
                key[1] = key[1].replaceAll("'", "");
                key[1] = key[1].replaceAll('"', '');

                while (par !== null && par !== undefined && !has_attr) {

                    this_attr = par.getAttribute(key[0]);
                    if (this_attr == key[1]) {
                        return par;
                    }
                    last_par = par;
                    par = par.parentNode;

                    try {
                        has_attr = par.hasAttribute(key[0]);
                    } catch {
                        has_attr = false
                    }

                }

            } else {
                let has_attr = par.hasAttribute(key);
                while (par !== null && par !== undefined && !has_attr) {
                    last_par = par;
                    par = par.parentNode;

                    try {
                        has_attr = par.hasAttribute(key);
                    } catch {
                        has_attr = false
                    }
                }
            }
            break;
        case 'class':
            let has_class = par.classList.contains(key)
            while (par !== null && par !== undefined && !has_class) {

                last_par = par;
                par = par.parentNode;

                try {
                    has_class = par.classList.contains(key);
                } catch {
                    has_class = false;
                }
            }
            break;
        default:
            return false;
            break;
    }

    if (par !== null && par !== undefined) {

        if (find_mode == 'hard_attr') {

            this_attr = par.getAttribute(key[0])
            if (this_attr == key[1]) {
                return par;
            } else return false;

        } else return par;

    } else {
        if (find_mode == 'hard_attr') {

            this_attr = last_par.getAttribute(key[0])
            if (this_attr == key[1]) {
                return par;
            } else return false;

        } else return last_par;
    }

}
jQuery(document).ready(function () {

    //Defines
    const mother = document.querySelector('div.woosbymdz-con');
    /***/

    /*set active loading effect*/
    var loading_selector = document.getElementById("woosbymdz-loading-effect");
    if (loading_selector !== null && loading_selector !== undefined) {
        var all_loading_options = loading_selector.querySelectorAll('option');
        var load_effect_testerdiv = document.getElementById('slidersloadeffectpreview-div');
        for (var i = 0; i < all_loading_options.length; i++) {

            if (all_loading_options[i].value == sliders_load_eff) {

                all_loading_options[i].setAttribute("selected", "");
                load_effect_testerdiv.classList.add('' + sliders_load_eff + '');

            }
        }
        var loading_selector_val = loading_selector.value;
        var tmp_wrapper = document.getElementById("woosbymdz-your-loading-fields");
        switch (loading_selector_val) {
            case 'effect-13':
                if (tmp_wrapper !== null && tmp_wrapper !== undefined) {
                    tmp_wrapper.classList.remove("mdz-hidden");
                }
                break;
            default:
                break;

        }
        var loadingeffect_selector = document.getElementById('woosbymdz-loading-effect');
        loadingeffect_selector.addEventListener('change', function () {

            var select_val = this.value;
            var current_option = loadingeffect_selector.options[loadingeffect_selector.selectedIndex].value;
            load_effect_testerdiv.className = '';
            load_effect_testerdiv.classList.add('slidersloadeffectpreview');
            load_effect_testerdiv.classList.add(current_option);

            var tmp_wrapper = document.getElementById("woosbymdz-your-loading-fields");
            switch (select_val) {
                case 'effect-13':
                    if (tmp_wrapper !== null && tmp_wrapper !== undefined) {
                        tmp_wrapper.classList.remove("mdz-hidden");
                    }
                    break;
                default:
                    if (tmp_wrapper !== null && tmp_wrapper !== undefined) {
                        tmp_wrapper.classList.add("mdz-hidden");
                    }
                    break;

            }

        });
    }

    /*expand & un-expand*/
    var sliders_effect_tester = document.getElementById('slidersloadeffectpreview-div');
    if (sliders_effect_tester !== null && sliders_effect_tester !== undefined) {
        sliders_effect_tester.addEventListener('click', function () {
            var is_expanded = sliders_effect_tester.getAttribute('expanded');
            var parent = sliders_effect_tester.parentNode;
            switch (is_expanded) {
                case '1':
                    sliders_effect_tester.setAttribute('expanded', '0');
                    sliders_effect_tester.style.height = "82px";
                    parent.style.width = "6%";
                    parent.classList.remove('expanded');
                    parent.classList.add('not-expanded');
                    break;
                case '0':
                    sliders_effect_tester.setAttribute('expanded', '1');
                    sliders_effect_tester.style.height = "200px";
                    parent.style.width = "100%";
                    parent.classList.add('expanded');
                    parent.classList.remove('not-expanded');
                    break;
            }

        });
    }

    /*night mode btns*/
    let mother_con = document.querySelector(".woosbymdz-con");
    var all_night_btns = document.getElementsByClassName("nightmode-btn");
    for (var i = 0; i < all_night_btns.length; i++) {
        all_night_btns[i].addEventListener('click', function () {
            var btn = this;

            let woosbymdz_con = document.querySelector(".woosbymdz-con");
            jQuery.ajax({
                url: admin_ajax_url,
                method: 'post',
                cache: false,
                data: {
                    action: 'woosbymdz_nightMode_ajax',
                    mode: 'dark',

                    shonafahmi: woosbymdz_settings_ajx_obj.shonafahmi,//Security
                },
                error: function () {
                    console.log("woosbymdz ajax error!");
                    btn.classList.remove("disabled-btn");
                    mother_con.classList.remove("disabled-btn");
                },
                beforeSend: function () {
                    btn.classList.add("disabled-btn");
                    mother_con.classList.add("disabled-btn");
                },
                success: function () {

                    var conf = confirm(conf_trans);
                    if (conf) {

                        location.reload();

                    } else {

                        btn.classList.remove("disabled-btn");
                        mother_con.classList.remove("disabled-btn");

                    }
                }
            });
        });
    }

    if(mother.classList.contains('nightmodeon')){
        var wp_content_tag = document.getElementById('wpcontent')

        if(wp_content_tag !== null && wp_content_tag !== undefined){
            wp_content_tag.classList.add('nightmodeon')
        }
    }
    /***/

    /*Expand and un-expand sections*/
    const all_fieldsets = document.querySelectorAll("fieldset");

    for (var ss = 0; ss < all_fieldsets.length; ss++) {
        var tmp_fieldset = all_fieldsets[ss];

        tmp_fieldset.addEventListener("click", function () {

            var btn = this.querySelector("span.dashicons.dashicons-plus");
            var is_closed = this.getAttribute("closed");
            if (is_closed == "yes") {
                this.setAttribute("closed", "no");
                btn.setAttribute("title", "بستن این بخش");
            }

            //change side-bar active btns
            var field_numb = this.getAttribute("numb");
            var connector_side_btn = mother.querySelector('.block-setting-connector[open-filed="' + field_numb + '"]')
            if (connector_side_btn !== null && connector_side_btn !== undefined) {
                connector_side_btn.classList.add('btn-sec-active');
            }
            /***/

        });

    }

    const all_closebtns = document.querySelectorAll(".acordion-btn span");

    for (var mm = 0; mm < all_closebtns.length; mm++) {
        var tmp_btn = all_closebtns[mm];

        tmp_btn.addEventListener("click", function () {

            let par = this.parentNode.parentNode;
            var is_closed = par.getAttribute("closed");
            if (is_closed == "no") {

                this.setAttribute("title", "باز کردن این بخش");
                setTimeout(function () {
                    par.setAttribute("closed", "yes");

                    //change side-bar active btns
                    var field_numb = par.getAttribute("numb");
                    var connector_side_btn = mother.querySelector('.block-setting-connector[open-filed="' + field_numb + '"]')

                    if (connector_side_btn !== null && connector_side_btn !== undefined) {
                        connector_side_btn.classList.remove('btn-sec-active');
                    }
                    /***/
                }, 50)



            }
        });
    }
    /*Theme Changer*/
    var theme_changer_btn = mother_con.querySelector(".woosbymdz-theme-changer");

    if (theme_changer_btn !== null && theme_changer_btn !== undefined) {

        theme_changer_btn.addEventListener("click", function () {

            var btn = this;
            jQuery.ajax({
                url: admin_ajax_url,
                method: 'post',
                cache: false,
                data: {
                    action: 'woosbymdz_nightMode_ajax',
                    mode: 'theme',

                    shonafahmi: woosbymdz_settings_ajx_obj.shonafahmi,//Security
                },
                error: function () {
                    console.log("woosbymdz ajax error!");
                    btn.classList.remove("disabled-btn");
                    mother_con.classList.remove("disabled-btn");
                },
                beforeSend: function () {
                    btn.classList.add("disabled-btn");
                    mother_con.classList.add("disabled-btn");
                },
                success: function () {

                    var conf = confirm(conf_trans2);
                    if (conf) {

                        location.reload();

                    } else {

                        btn.classList.remove("disabled-btn");
                        mother_con.classList.remove("disabled-btn");

                    }

                }
            });

        });
    }
    /**/

    /*All Select tags*/
    var all_other_selects = document.querySelectorAll("[active-opt]");
    for (var sel = 0; sel < all_other_selects.length; sel++) {

        if (all_other_selects[sel] !== null && all_other_selects[sel] !== undefined) {

            var active = all_other_selects[sel].getAttribute("active-opt");
            var active_opt = all_other_selects[sel].querySelector("option[value= '" + active + "']");
            if (active_opt !== null && active_opt !== undefined) {
                active_opt.setAttribute("selected", "");
            }
        }
    }
    /**/


    /*Sticky Elements*/
    var all_stickers = mother.querySelectorAll("[woosbymdz-sticky]");
    if (all_stickers.length > 0) {

        for (var o = 0; o < all_stickers.length; o++) {
            all_stickers[o].setAttribute("where-sticked", "");
        }



        window.addEventListener("scroll", function () {

            var top = window.scrollY;
            for (var i = 0; i < all_stickers.length; i++) {

                if (all_stickers[i] !== null && all_stickers[i] !== undefined) {

                    var has_stop_parent = false;
                    let stop_parent = woosbymdz_finpar(all_stickers[i], "[woosbymdz-stick]");
                    var stop_width = all_stickers[i].getAttribute("woosbymdz-sticky-stop-wid");
                    stop_width = parseInt(stop_width);

                    if (this.window.innerWidth <= stop_width) { return; }


                    if (stop_parent !== document) {
                        has_stop_parent = true;
                    }

                    var this_top = all_stickers[i].offsetTop;

                    if (has_stop_parent && (stop_parent.offsetHeight) <= (all_stickers[i].offsetHeight + 255)) {
                        all_stickers[i].classList.remove("woosbymdz-sticked");
                        return;
                    }

                    let this_offset = '';
                    if (all_stickers[i].hasAttribute("woosbymdz-sticky")) {
                        this_offset = all_stickers[i].getAttribute("woosbymdz-sticky");
                    }
                    if (all_stickers[i].hasAttribute("woosbymdz-sticker")) {
                        this_offset = all_stickers[i].getAttribute("woosbymdz-sticker");
                    }

                    if (this_offset.trim() == "") {
                        this_offset = 0;
                    }

                    var stop_pos = all_stickers[i].getAttribute("where-sticked");

                    var stop_pos_num = parseInt(stop_pos);

                    if (top >= this_top && stop_pos.trim() == '') {

                        all_stickers[i].setAttribute("where-sticked", this_top);
                        var elem_left_pos = all_stickers[i].getBoundingClientRect().left;
                        var elem_width = all_stickers[i].offsetWidth;

                        all_stickers[i].classList.add("woosbymdz-sticked");
                        all_stickers[i].style.top = this_offset + "px";
                        all_stickers[i].style.left = elem_left_pos + "px";
                        all_stickers[i].style.maxWidth = elem_width + "px";
                    }

                    if (stop_pos.trim() != '' && top < stop_pos_num) {
                        all_stickers[i].classList.remove("woosbymdz-sticked");
                        all_stickers[i].setAttribute("where-sticked", "");
                    }

                    if (has_stop_parent) {

                        var par_height = stop_parent.offsetHeight;
                        var par_top = stop_parent.offsetTop;
                        var threshold = all_stickers[i].offsetHeight;;
                        var parent_bottom = (par_top + par_height) - threshold;

                        if (stop_pos.trim() != '' && top >= parent_bottom) {
                            stop_parent.classList.add("ended")
                            all_stickers[i].classList.add("ended");
                            all_stickers[i].style.left = "unset";
                        }
                        if (stop_pos.trim() != '' && top < parent_bottom) {
                            stop_parent.classList.remove("ended")
                            all_stickers[i].classList.remove("ended");
                        }
                    }

                }
            }

        })

    }
    /***/

    /*Side bar Btns*/
    var all_side_btns = mother.querySelectorAll('.block-setting-connector[open-filed]');
    for (var gh = 0; gh < all_side_btns.length; gh++) {
        all_side_btns[gh].addEventListener('click', function () {

            let this_btn = this;
            var all_filed_sets = mother.querySelectorAll('fieldset');

            //Reseting
            for (var u = 0; u < all_filed_sets.length; u++) {
                all_filed_sets[u].setAttribute('closed', 'yes');
            }
            for (var p = 0; p < all_side_btns.length; p++) {
                all_side_btns[p].classList.remove('btn-sec-active')
            }
            /***/

            var sec_to_open = this_btn.getAttribute('open-filed');
            this_btn.classList.add('btn-sec-active')

            for (var u = 0; u < all_filed_sets.length; u++) {
                var filed_numb = all_filed_sets[u].getAttribute('numb');
                if (filed_numb == sec_to_open) {
                    all_filed_sets[u].setAttribute('closed', 'no');

                    var dom_top = all_filed_sets[u].offsetTop;

                    window.scroll(0, dom_top)

                }
            }
        })
    }
    /***/

    //Elementor-pro restore-file contents btn  
    var tmp_btn = document.getElementById('rollback-elempro-file')  
    if(tmp_btn !== null && tmp_btn !== undefined){

        
        tmp_btn.addEventListener('click', function(){

            var btn = this;
            jQuery.ajax({
                url: admin_ajax_url,
                method: 'post',
                cache: false,
                data: {
                    action: 'woosbymdz_elmpro_restore_file',

                    shonafahmi: woosbymdz_settings_ajx_obj.shonafahmi,//Security
                },
                error: function () {
                    console.log("woosbymdz ajax error! (Elementor-pro restore-file contents btn)");
                    btn.classList.remove("disabled-btn");
                    mother_con.classList.remove("disabled-btn");
                },
                beforeSend: function () {
                    btn.classList.add("disabled-btn");
                    mother_con.classList.add("disabled-btn");
                },
                success: function (resp) {

                    btn.classList.remove("disabled-btn");
                    mother_con.classList.remove("disabled-btn");

                    if(resp == 'done'){
                        alert(conf_trans4);
                    }else if(resp == 'error'){
                        alert(conf_trans5);
                    }else {

                        
                        if(resp.success !== null && resp.success !== undefined){
                            if(resp.success == 'false' || !resp.success){

                                console.log(resp.success)

                                alert(conf_trans3);
                            }
                        }
                    }

                    location.reload();
                    
                    console.log(resp);
                }
            });
        })
    }
    // /***/
})