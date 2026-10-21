<!-- <style>
      :root { 
        --color: {{all_settings['color']}};
        --color_o: {{all_settings['color']}}1a;
        --color_dark: {{all_settings['color_dark']}};
        --color_dark_o: {{all_settings['color_dark']}}1a;
      }
    </style> -->
<?php
$all_settings->departments = explode(",",urldecode($all_settings->departments));
?>

<div id="cr-dashboard" data-ng-init="loaded()" class="cr-dashboard"
  :class="{'dark' : dark_mode ,'mini' : menu == 'mini'}">

  <div id="log-out" ng-class="{'active' :  logout_popup }">

    <div class="question">
      آیا می‌خواهید از حساب کاربری خود خارج شوید؟
      <div class="btns">
        <a href="<?php echo $log_out_link; ?>">بله</a>
        <button @click="close_back_drop();" type="button">خیر</button>
      </div>
    </div>
  </div>
  <div ng-class="{'active' : menu == 'active' || logout_popup || notification_popup }" @click="close_back_drop()"
    class="back-drop"></div>
  <div class="container">
    <div class="panel">
      <div class="row">
        <div class="col-12 col-md-2">
          <a href="<?php echo get_site_url(); ?>" target="_blank">
            <img class="logo"
              src="https://static.vecteezy.com/system/resources/previews/002/318/271/large_2x/user-profile-icon-free-vector.jpg"
              alt="">
          </a>
 
          <ul class="cr-dashboard-menu">
            <li v-for="n in menu_items" :class="route == ('/'+n)?'active':''  ">
              <router-link v-if="n == 'link_1' && dashboard_settings['link_1_active'] == 'on'"
                @click="menu_mobile_close()" :to="'/panel/'+dashboard_settings['link_1_link']"
                :target="(dashboard_settings['link_1_newtab'] == 'on') ? '_blank' : undefined">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.076 0h3.385A2.549 2.549 0 0 1 20 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56h-3.385a2.549 2.549 0 0 1-2.539-2.56V2.56A2.549 2.549 0 0 1 14.076 0"
                      opacity=".4" />
                    <path
                      d="M2.539 0h3.385a2.549 2.549 0 0 1 2.539 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56H2.54A2.549 2.549 0 0 1 0 5.974V2.56A2.549 2.549 0 0 1 2.539 0m0 11.466h3.385a2.549 2.549 0 0 1 2.539 2.56v3.414A2.55 2.55 0 0 1 5.924 20H2.54A2.55 2.55 0 0 1 0 17.44v-3.415a2.549 2.549 0 0 1 2.539-2.56m11.537 0h3.385A2.549 2.549 0 0 1 20 14.026v3.415A2.55 2.55 0 0 1 17.461 20h-3.385a2.55 2.55 0 0 1-2.539-2.56v-3.415a2.549 2.549 0 0 1 2.539-2.56" />
                  </g>
                </svg>
                {{dashboard_settings['link_1_label']}}
              </router-link>
              <router-link v-if="n == 'link_2' && dashboard_settings['link_2_active'] == 'on'"
                @click="menu_mobile_close()" :to="'/panel/'+dashboard_settings['link_2_link']"
                :target="(dashboard_settings['link_2_newtab'] == 'on') ? '_blank' : undefined">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.076 0h3.385A2.549 2.549 0 0 1 20 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56h-3.385a2.549 2.549 0 0 1-2.539-2.56V2.56A2.549 2.549 0 0 1 14.076 0"
                      opacity=".4" />
                    <path
                      d="M2.539 0h3.385a2.549 2.549 0 0 1 2.539 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56H2.54A2.549 2.549 0 0 1 0 5.974V2.56A2.549 2.549 0 0 1 2.539 0m0 11.466h3.385a2.549 2.549 0 0 1 2.539 2.56v3.414A2.55 2.55 0 0 1 5.924 20H2.54A2.55 2.55 0 0 1 0 17.44v-3.415a2.549 2.549 0 0 1 2.539-2.56m11.537 0h3.385A2.549 2.549 0 0 1 20 14.026v3.415A2.55 2.55 0 0 1 17.461 20h-3.385a2.55 2.55 0 0 1-2.539-2.56v-3.415a2.549 2.549 0 0 1 2.539-2.56" />
                  </g>
                </svg>
                {{dashboard_settings['link_2_label']}}
              </router-link>
              <router-link v-if="n == 'link_3' && dashboard_settings['link_3_active'] == 'on'"
                @click="menu_mobile_close()" :to="'/panel/'+dashboard_settings['link_3_link']"
                :target="(dashboard_settings['link_3_newtab'] == 'on') ? '_blank' : undefined">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.076 0h3.385A2.549 2.549 0 0 1 20 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56h-3.385a2.549 2.549 0 0 1-2.539-2.56V2.56A2.549 2.549 0 0 1 14.076 0"
                      opacity=".4" />
                    <path
                      d="M2.539 0h3.385a2.549 2.549 0 0 1 2.539 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56H2.54A2.549 2.549 0 0 1 0 5.974V2.56A2.549 2.549 0 0 1 2.539 0m0 11.466h3.385a2.549 2.549 0 0 1 2.539 2.56v3.414A2.55 2.55 0 0 1 5.924 20H2.54A2.55 2.55 0 0 1 0 17.44v-3.415a2.549 2.549 0 0 1 2.539-2.56m11.537 0h3.385A2.549 2.549 0 0 1 20 14.026v3.415A2.55 2.55 0 0 1 17.461 20h-3.385a2.55 2.55 0 0 1-2.539-2.56v-3.415a2.549 2.549 0 0 1 2.539-2.56" />
                  </g>
                </svg>
                {{dashboard_settings['link_3_label']}}
              </router-link>
              <router-link v-if="n == 'link_4' && dashboard_settings['link_4_active'] == 'on'"
                @click="menu_mobile_close()" :to="'/panel/'+dashboard_settings['link_4_link']"
                :target="(dashboard_settings['link_4_newtab'] == 'on') ? '_blank' : undefined">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.076 0h3.385A2.549 2.549 0 0 1 20 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56h-3.385a2.549 2.549 0 0 1-2.539-2.56V2.56A2.549 2.549 0 0 1 14.076 0"
                      opacity=".4" />
                    <path
                      d="M2.539 0h3.385a2.549 2.549 0 0 1 2.539 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56H2.54A2.549 2.549 0 0 1 0 5.974V2.56A2.549 2.549 0 0 1 2.539 0m0 11.466h3.385a2.549 2.549 0 0 1 2.539 2.56v3.414A2.55 2.55 0 0 1 5.924 20H2.54A2.55 2.55 0 0 1 0 17.44v-3.415a2.549 2.549 0 0 1 2.539-2.56m11.537 0h3.385A2.549 2.549 0 0 1 20 14.026v3.415A2.55 2.55 0 0 1 17.461 20h-3.385a2.55 2.55 0 0 1-2.539-2.56v-3.415a2.549 2.549 0 0 1 2.539-2.56" />
                  </g>
                </svg>
                {{dashboard_settings['link_4_label']}}
              </router-link>
              <router-link v-if="n == 'link_5' && dashboard_settings['link_5_active'] == 'on'"
                @click="menu_mobile_close()" :to="'/panel/'+dashboard_settings['link_5_link']"
                :target="(dashboard_settings['link_5_newtab'] == 'on') ? '_blank' : undefined">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.076 0h3.385A2.549 2.549 0 0 1 20 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56h-3.385a2.549 2.549 0 0 1-2.539-2.56V2.56A2.549 2.549 0 0 1 14.076 0"
                      opacity=".4" />
                    <path
                      d="M2.539 0h3.385a2.549 2.549 0 0 1 2.539 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56H2.54A2.549 2.549 0 0 1 0 5.974V2.56A2.549 2.549 0 0 1 2.539 0m0 11.466h3.385a2.549 2.549 0 0 1 2.539 2.56v3.414A2.55 2.55 0 0 1 5.924 20H2.54A2.55 2.55 0 0 1 0 17.44v-3.415a2.549 2.549 0 0 1 2.539-2.56m11.537 0h3.385A2.549 2.549 0 0 1 20 14.026v3.415A2.55 2.55 0 0 1 17.461 20h-3.385a2.55 2.55 0 0 1-2.539-2.56v-3.415a2.549 2.549 0 0 1 2.539-2.56" />
                  </g>
                </svg>
                {{dashboard_settings['link_5_label']}}
              </router-link>
              <router-link v-if="n == 'html_1' && dashboard_settings['html_1_active'] == 'on'"
                @click="menu_mobile_close();set_html(1);" :to="'/panel/'+dashboard_settings['html_1_link']">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.076 0h3.385A2.549 2.549 0 0 1 20 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56h-3.385a2.549 2.549 0 0 1-2.539-2.56V2.56A2.549 2.549 0 0 1 14.076 0"
                      opacity=".4" />
                    <path
                      d="M2.539 0h3.385a2.549 2.549 0 0 1 2.539 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56H2.54A2.549 2.549 0 0 1 0 5.974V2.56A2.549 2.549 0 0 1 2.539 0m0 11.466h3.385a2.549 2.549 0 0 1 2.539 2.56v3.414A2.55 2.55 0 0 1 5.924 20H2.54A2.55 2.55 0 0 1 0 17.44v-3.415a2.549 2.549 0 0 1 2.539-2.56m11.537 0h3.385A2.549 2.549 0 0 1 20 14.026v3.415A2.55 2.55 0 0 1 17.461 20h-3.385a2.55 2.55 0 0 1-2.539-2.56v-3.415a2.549 2.549 0 0 1 2.539-2.56" />
                  </g>
                </svg>
                {{dashboard_settings['html_1_label']}}
              </router-link>
              <router-link v-if="n == 'html_2' && dashboard_settings['html_2_active'] == 'on'"
                @click="menu_mobile_close();set_html(2);" :to="'/panel/'+dashboard_settings['html_2_link']">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.076 0h3.385A2.549 2.549 0 0 1 20 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56h-3.385a2.549 2.549 0 0 1-2.539-2.56V2.56A2.549 2.549 0 0 1 14.076 0"
                      opacity=".4" />
                    <path
                      d="M2.539 0h3.385a2.549 2.549 0 0 1 2.539 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56H2.54A2.549 2.549 0 0 1 0 5.974V2.56A2.549 2.549 0 0 1 2.539 0m0 11.466h3.385a2.549 2.549 0 0 1 2.539 2.56v3.414A2.55 2.55 0 0 1 5.924 20H2.54A2.55 2.55 0 0 1 0 17.44v-3.415a2.549 2.549 0 0 1 2.539-2.56m11.537 0h3.385A2.549 2.549 0 0 1 20 14.026v3.415A2.55 2.55 0 0 1 17.461 20h-3.385a2.55 2.55 0 0 1-2.539-2.56v-3.415a2.549 2.549 0 0 1 2.539-2.56" />
                  </g>
                </svg>
                {{dashboard_settings['html_2_label']}}
              </router-link>
              <router-link v-if="n == 'html_3' && dashboard_settings['html_3_active'] == 'on'"
                @click="menu_mobile_close();set_html(3);" :to="'/panel/'+dashboard_settings['html_3_link']">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.076 0h3.385A2.549 2.549 0 0 1 20 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56h-3.385a2.549 2.549 0 0 1-2.539-2.56V2.56A2.549 2.549 0 0 1 14.076 0"
                      opacity=".4" />
                    <path
                      d="M2.539 0h3.385a2.549 2.549 0 0 1 2.539 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56H2.54A2.549 2.549 0 0 1 0 5.974V2.56A2.549 2.549 0 0 1 2.539 0m0 11.466h3.385a2.549 2.549 0 0 1 2.539 2.56v3.414A2.55 2.55 0 0 1 5.924 20H2.54A2.55 2.55 0 0 1 0 17.44v-3.415a2.549 2.549 0 0 1 2.539-2.56m11.537 0h3.385A2.549 2.549 0 0 1 20 14.026v3.415A2.55 2.55 0 0 1 17.461 20h-3.385a2.55 2.55 0 0 1-2.539-2.56v-3.415a2.549 2.549 0 0 1 2.539-2.56" />
                  </g>
                </svg>
                {{dashboard_settings['html_3_label']}}
              </router-link>
              <router-link v-if="n == 'html_4' && dashboard_settings['html_4_active'] == 'on'"
                @click="menu_mobile_close();set_html(4);" :to="'/panel/'+dashboard_settings['html_4_link']">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.076 0h3.385A2.549 2.549 0 0 1 20 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56h-3.385a2.549 2.549 0 0 1-2.539-2.56V2.56A2.549 2.549 0 0 1 14.076 0"
                      opacity=".4" />
                    <path
                      d="M2.539 0h3.385a2.549 2.549 0 0 1 2.539 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56H2.54A2.549 2.549 0 0 1 0 5.974V2.56A2.549 2.549 0 0 1 2.539 0m0 11.466h3.385a2.549 2.549 0 0 1 2.539 2.56v3.414A2.55 2.55 0 0 1 5.924 20H2.54A2.55 2.55 0 0 1 0 17.44v-3.415a2.549 2.549 0 0 1 2.539-2.56m11.537 0h3.385A2.549 2.549 0 0 1 20 14.026v3.415A2.55 2.55 0 0 1 17.461 20h-3.385a2.55 2.55 0 0 1-2.539-2.56v-3.415a2.549 2.549 0 0 1 2.539-2.56" />
                  </g>
                </svg>
                {{dashboard_settings['html_4_label']}}
              </router-link>
              <router-link v-if="n == 'html_5' && dashboard_settings['html_5_active'] == 'on'"
                @click="menu_mobile_close();set_html(5);" :to="'/panel/'+dashboard_settings['html_5_link']">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.076 0h3.385A2.549 2.549 0 0 1 20 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56h-3.385a2.549 2.549 0 0 1-2.539-2.56V2.56A2.549 2.549 0 0 1 14.076 0"
                      opacity=".4" />
                    <path
                      d="M2.539 0h3.385a2.549 2.549 0 0 1 2.539 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56H2.54A2.549 2.549 0 0 1 0 5.974V2.56A2.549 2.549 0 0 1 2.539 0m0 11.466h3.385a2.549 2.549 0 0 1 2.539 2.56v3.414A2.55 2.55 0 0 1 5.924 20H2.54A2.55 2.55 0 0 1 0 17.44v-3.415a2.549 2.549 0 0 1 2.539-2.56m11.537 0h3.385A2.549 2.549 0 0 1 20 14.026v3.415A2.55 2.55 0 0 1 17.461 20h-3.385a2.55 2.55 0 0 1-2.539-2.56v-3.415a2.549 2.549 0 0 1 2.539-2.56" />
                  </g>
                </svg>
                {{dashboard_settings['html_5_label']}}
              </router-link>
              <router-link v-if="n == 'dashboard' && dashboard_settings['dashboard_active'] == 'on'"
                @click="menu_mobile_close();set_html(1);" to="/panel/dashboard" type="button">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.076 0h3.385A2.549 2.549 0 0 1 20 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56h-3.385a2.549 2.549 0 0 1-2.539-2.56V2.56A2.549 2.549 0 0 1 14.076 0"
                      opacity=".4" />
                    <path
                      d="M2.539 0h3.385a2.549 2.549 0 0 1 2.539 2.56v3.415a2.549 2.549 0 0 1-2.539 2.56H2.54A2.549 2.549 0 0 1 0 5.974V2.56A2.549 2.549 0 0 1 2.539 0m0 11.466h3.385a2.549 2.549 0 0 1 2.539 2.56v3.414A2.55 2.55 0 0 1 5.924 20H2.54A2.55 2.55 0 0 1 0 17.44v-3.415a2.549 2.549 0 0 1 2.539-2.56m11.537 0h3.385A2.549 2.549 0 0 1 20 14.026v3.415A2.55 2.55 0 0 1 17.461 20h-3.385a2.55 2.55 0 0 1-2.539-2.56v-3.415a2.549 2.549 0 0 1 2.539-2.56" />
                  </g>
                </svg>
                {{dashboard_settings['dashboard_label']}}
              </router-link>
              <router-link v-if="n == 'orders' && dashboard_settings['orders_active'] == 'on'" to="/panel/orders"
                @click="menu_mobile_close()" type="button">
                <svg width="18" height="20" viewBox="0 0 18 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M14.086 5c1.264 0 2.59.91 3.058 3.12l.77 6.195C18.478 18.354 16.207 20 13.157 20h-8.29c-3.057 0-5.4-1.137-4.763-5.685L.883 8.12C1.28 5.846 2.65 5 3.932 5zM6.097 8.33a.897.897 0 0 0-.884.909c0 .502.396.91.884.91a.897.897 0 0 0 .884-.91l-.007-.114a.893.893 0 0 0-.877-.796zm5.789 0a.897.897 0 0 0-.884.909c0 .502.396.91.884.91a.897.897 0 0 0 .884-.91.897.897 0 0 0-.884-.91z" />
                    <path
                      d="M13.974 4.774A.502.502 0 0 1 13.93 5h-1.437a.649.649 0 0 1-.043-.226 3.478 3.478 0 0 0-3.484-3.472 3.478 3.478 0 0 0-3.484 3.472.649.649 0 0 1 0 .226H4.01a.649.649 0 0 1 0-.226A4.992 4.992 0 0 1 9.005 0 4.992 4.992 0 0 1 14 4.774h-.026z"
                      opacity=".4" />
                  </g>
                </svg>
                {{dashboard_settings['orders_label']}}
              </router-link>
              <router-link v-if="n == 'addresses' && dashboard_settings['address_active'] == 'on'" to="/panel/addresses"
                @click="menu_mobile_close()" type="button">
                <svg width="14" height="20" viewBox="0 0 14 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M3.532.937a6.89 6.89 0 0 1 7.034.058C12.71 2.327 14.012 4.705 14 7.26c-.05 2.54-1.447 4.929-3.193 6.775a18.726 18.726 0 0 1-3.358 2.82A1.174 1.174 0 0 1 7.04 17a.82.82 0 0 1-.39-.119 18.515 18.515 0 0 1-4.839-4.547A9.28 9.28 0 0 1 0 7.134l.005-.273c.09-2.456 1.42-4.7 3.527-5.924zm4.375 4.098a2.346 2.346 0 0 0-2.594.52 2.455 2.455 0 0 0-.519 2.64 2.378 2.378 0 0 0 2.198 1.497 2.339 2.339 0 0 0 1.683-.702c.446-.453.696-1.07.694-1.712a2.423 2.423 0 0 0-1.462-2.243z" />
                    <ellipse opacity=".4" cx="7" cy="19" rx="5" ry="1" />
                  </g>
                </svg>
                {{dashboard_settings['address_label']}}
              </router-link>
              <router-link v-if="n == 'notifications' && dashboard_settings['notifications_active'] == 'on'"
                @click="menu_mobile_close()" to="/panel/notifications" type="button">
                <svg width="17" height="20" viewBox="0 0 17 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M16.27 9.645c-.731-.853-1.063-1.592-1.063-2.848V6.37c0-1.636-.377-2.69-1.196-3.745C12.75.987 10.624 0 8.544 0h-.088C6.419 0 4.36.942 3.077 2.513 2.213 3.588 1.793 4.688 1.793 6.37v.427c0 1.256-.31 1.995-1.063 2.848-.553.629-.73 1.437-.73 2.31 0 .876.287 1.705.864 2.379a4.532 4.532 0 0 0 2.9 1.413c1.571.179 3.142.246 4.736.246 1.594 0 3.165-.112 4.737-.246a4.532 4.532 0 0 0 2.9-1.413A3.614 3.614 0 0 0 17 11.956c0-.874-.177-1.682-.73-2.31" />
                    <path
                      d="M10.509 17.228c-.5-.106-3.546-.106-4.046 0-.428.1-.89.329-.89.832.025.48.306.905.696 1.174h-.001a3.635 3.635 0 0 0 1.714.733c.33.045.666.043 1.008 0 .618-.09 1.21-.34 1.713-.732v-.001c.39-.27.67-.693.695-1.174 0-.503-.462-.733-.89-.832"
                      opacity=".4" />
                  </g>
                </svg>
                {{dashboard_settings['notifications_label']}}
              </router-link>
              <router-link v-if="n == 'downloads' && dashboard_settings['downloads_active'] == 'on'"
                to="/panel/downloads" @click="menu_mobile_close()" type="button">
                <svg width="20" height="19" viewBox="0 0 20 19" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M15.554 4.796c2.451 0 4.446 2.06 4.446 4.592v5.032c0 2.525-1.99 4.58-4.436 4.58H4.448C1.996 19 0 16.941 0 14.41V9.377c0-2.525 1.991-4.58 4.438-4.58h11.116z"
                      opacity=".4" />
                    <path
                      d="m10.546 13.537 2.91-2.968c.3-.306.3-.8-.003-1.106a.762.762 0 0 0-1.089.002l-1.593 1.626V.782a.777.777 0 0 0-.77-.782.776.776 0 0 0-.77.782v10.309L7.637 9.465a.762.762 0 0 0-1.089-.002.788.788 0 0 0-.002 1.106l2.91 2.968c.144.148.34.231.544.231a.766.766 0 0 0 .546-.23" />
                  </g>
                </svg>
                {{dashboard_settings['downloads_label']}}
              </router-link>
              <router-link v-if="n == 'settings' && dashboard_settings['settings_active'] == 'on'" to="/panel/settings"
                @click="menu_mobile_close()" type="button">
                <svg width="19" height="20" viewBox="0 0 19 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M9.512 12.83c-1.604 0-2.902-1.25-2.902-2.82 0-1.57 1.298-2.83 2.902-2.83 1.605 0 2.872 1.26 2.872 2.83s-1.267 2.82-2.872 2.82" />
                    <path
                      d="M18.73 12.37c-.194-.3-.47-.6-.828-.79-.286-.14-.47-.37-.633-.64-.521-.86-.215-1.99.654-2.5a2.027 2.027 0 0 0 .756-2.83l-.685-1.18a2.112 2.112 0 0 0-2.871-.76c-.9.48-2.055.16-2.576-.69-.163-.28-.255-.58-.235-.88.03-.39-.092-.76-.276-1.06A2.152 2.152 0 0 0 10.217 0h-1.44c-.747.02-1.431.42-1.81 1.04-.194.3-.306.67-.286 1.06.02.3-.071.6-.235.88-.521.85-1.676 1.17-2.565.69A2.124 2.124 0 0 0 1 4.43L.314 5.61c-.582.99-.255 2.26.757 2.83.868.51 1.175 1.64.664 2.5-.174.27-.358.5-.644.64-.347.19-.654.49-.817.79-.379.62-.358 1.4.02 2.05l.705 1.2a2.134 2.134 0 0 0 1.82 1.04c.347 0 .756-.1 1.083-.3.255-.17.562-.23.899-.23 1.012 0 1.86.83 1.88 1.82 0 1.15.94 2.05 2.126 2.05h1.39c1.175 0 2.115-.9 2.115-2.05.03-.99.88-1.82 1.89-1.82.328 0 .634.06.9.23.327.2.726.3 1.083.3.726 0 1.431-.4 1.81-1.04l.715-1.2c.368-.67.398-1.43.02-2.05"
                      opacity=".4" />
                  </g>
                </svg>

                {{dashboard_settings['settings_label']}}
              </router-link>


              <router-link v-if="n == 'membership' && dashboard_settings['membership_active'] == 'on'"
                to="/panel/membership" @click="menu_mobile_close()" type="button">
                <svg width="20" height="19" viewBox="0 0 20 19" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M15.554 4.796c2.451 0 4.446 2.06 4.446 4.592v5.032c0 2.525-1.99 4.58-4.436 4.58H4.448C1.996 19 0 16.941 0 14.41V9.377c0-2.525 1.991-4.58 4.438-4.58h11.116z"
                      opacity=".4" />
                    <path
                      d="m10.546 13.537 2.91-2.968c.3-.306.3-.8-.003-1.106a.762.762 0 0 0-1.089.002l-1.593 1.626V.782a.777.777 0 0 0-.77-.782.776.776 0 0 0-.77.782v10.309L7.637 9.465a.762.762 0 0 0-1.089-.002.788.788 0 0 0-.002 1.106l2.91 2.968c.144.148.34.231.544.231a.766.766 0 0 0 .546-.23" />
                  </g>
                </svg>
                {{dashboard_settings['membership_label']}}
              </router-link>



              <router-link v-if="n == 'tickets' && dashboard_settings['tickets_active'] == 'on'" to="/panel/support"
                @click="menu_mobile_close()" type="button">


                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                  viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">

                  <path class="st0" fill="#505050" d="M3.8,19H3c-0.8,0-1.6-0.3-2.1-0.9C0.3,17.6,0,16.8,0,16v-3C0,5.8,5.4,0,12,0s12,5.8,12,13v3
                    c0,0.8-0.3,1.6-0.9,2.1C22.6,18.7,21.8,19,21,19h-0.8c-2.7,4.6-6.9,5-8.2,5S6.5,23.6,3.8,19z M20,13.1c-0.6,0.2-1.3-0.2-1.3-0.8
                    c0-0.3,0-0.6-0.1-0.9c-1.8-0.3-3.6-1.9-3.7-3.7c-1.3,2-4.5,4.2-7,4.2C9,11.2,9.8,9.1,9.8,8.4c-0.6,1.1-2.9,3.1-4.6,3.5l0,0.5
                    c0,0.6-0.7,1-1.3,0.8v3.8c0.3,0.3,0.6,0.5,1,0.6c0.2,0.1,0.4,0.2,0.5,0.4C7.8,22,11,22,12,22c0.9,0,3.7,0,5.9-3.2
                    c-1.1,0.4-2.3,0.6-3.8,0.7c-0.4,0.6-1.2,1-2.1,1c-1.3,0-2.3-0.8-2.3-1.8s1-1.8,2.3-1.8c0.9,0,1.7,0.4,2.1,1c1.9,0,4.3-0.3,5.9-2.6
                    C20,15.4,20,13.1,20,13.1z M9,12.5c0.7,0,1.2,0.7,1.2,1.5S9.7,15.5,9,15.5S7.8,14.8,7.8,14S8.3,12.5,9,12.5z M15,12.5
                    c0.7,0,1.2,0.7,1.2,1.5s-0.6,1.5-1.2,1.5s-1.2-0.7-1.2-1.5S14.3,12.5,15,12.5z M20.5,11h1.4C21,5.9,16.9,2,12,2s-9,3.9-9.8,9h1.4
                    C4,7,6.9,3.3,12,3.3S20,7,20.5,11z" />
                </svg>

                {{dashboard_settings['tickets_label']}}
              </router-link>




              <button @click="logout_popup_function();menu_mobile_close()"
                v-if="n == 'logout' && dashboard_settings['logout_active'] == 'on'" type="button">
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <g fill="#505050" fill-rule="nonzero">
                    <path
                      d="M0 4.447C0 1.996 2.03 0 4.525 0h4.96C11.976 0 14 1.99 14 4.437v11.116C14 18.005 11.97 20 9.474 20H4.515C2.025 20 0 18.01 0 15.563V4.447z"
                      opacity=".4" />
                    <path
                      d="m19.779 9.455-2.846-2.91a.736.736 0 0 0-1.06.003.785.785 0 0 0 .001 1.089l1.56 1.593H7.549a.76.76 0 0 0-.75.77c0 .426.336.77.75.77h9.885l-1.56 1.593a.785.785 0 0 0-.001 1.089.74.74 0 0 0 .531.227.74.74 0 0 0 .53-.225l2.845-2.908a.783.783 0 0 0 0-1.091" />
                  </g>
                </svg>

                {{dashboard_settings['logout_label']}}
              </button>
              <div :style="'height:'+dashboard_settings['divider_label']+'px'"
                v-if="n == 'divider' && dashboard_settings['divider_active'] == 'on'" class="divider">

              </div>
            </li>



          </ul>
        </div>
        <div class="col-12 col-md-8">
          <div class="pages-section">
            <div class="header">
              <p class="title">
                <i class="icon"></i>
                داشبورد
              </p>
              <div class="icons">
                <button type="button" ><i class="icon notification"></i></button>
              </div>
            </div>
            <router-view></router-view>
           
          </div>
        </div>
        <div class="col-12 col-md-2">
          <div class="user-side">
            <img
              src="https://static.vecteezy.com/system/resources/previews/002/318/271/large_2x/user-profile-icon-free-vector.jpg"
              alt="">
            <p class="name">علیرضا صادقی</p>
            <p class="mobile">0912 654 4565</p>
            <div class="wallet">
              <span class="label">موجودی</span>
              <p v-html="dashboard_data.wallet.balance" class="price"></p>
              <router-link to="/panel/charge" >
                افزایش موجودی
    </router-link>
         
            </div>
            <router-link to="/panel/settings" >
           ویرایش اطلاعات کاربری
    </router-link>
    <router-link to="/panel/password" >
           تغییر رمز عبور
    </router-link>
            <button type="button">خروج از حساب</button>
          </div>


        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo plugin_dir_url( __FILE__ ).'/assets/components/login.vue.js'; ?>"></script>
<script src="<?php echo plugin_dir_url( __FILE__ ).'/assets/components/dashboard.vue.js'; ?>"></script>
<script src="<?php echo plugin_dir_url( __FILE__ ).'/assets/components/addresses.vue.js'; ?>"></script>
<script src="<?php echo plugin_dir_url( __FILE__ ).'/assets/components/orders.vue.js'; ?>"></script>
<script src="<?php echo plugin_dir_url( __FILE__ ).'/assets/components/profile.vue.js'; ?>"></script>
<script src="<?php echo plugin_dir_url( __FILE__ ).'/assets/components/support.vue.js'; ?>"></script>
<script src="<?php echo plugin_dir_url( __FILE__ ).'/assets/components/transactions.vue.js'; ?>"></script>
<script src="<?php echo plugin_dir_url( __FILE__ ).'/assets/components/charge.vue.js'; ?>"></script>
<script src="<?php echo plugin_dir_url( __FILE__ ).'/assets/components/settings.vue.js'; ?>"></script>
<script>

Vue.component("Login", Login);
Vue.component("Dashboard", Dashboard);
Vue.component("Orders", Orders);
Vue.component("Addresses", Addresses);
Vue.component("Profile", Profile);
Vue.component("Support", Support);
Vue.component("Transactions", Transactions);
Vue.component("Charge", Charge);
Vue.component("Settings", Settings);

    function getCookie(cname) {
      let name = cname + "=";
      let decodedCookie = decodeURIComponent(document.cookie);
      let ca = decodedCookie.split(';');
      for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
          c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length);
        }
      }
      return "";
    }

    const Foo = { template: '<div>foo</div>' }
const Bar = { template: '<div>bar</div>' }






const routes = [

  { path: '/panel', component: Dashboard ,
    props: {  dashboard_data: JSON.parse(`<?php echo json_encode($dashboard); ?>`)} },
  { path: '/panel/dashboard', component: Dashboard , 
    props: {  dashboard_data: JSON.parse(`<?php echo json_encode($dashboard); ?>`)}},
  { path: '/panel/orders', component: Orders,
    props: {  dashboard_data: JSON.parse(`<?php echo json_encode($dashboard); ?>`)} },
  { path: '/panel/Addresses', component: Addresses,
    props: { provinces : JSON.parse(` <?php echo json_encode($ostan); ?>`),addresses : JSON.parse(` <?php echo json_encode($addresses); ?>`),cities : JSON.parse(' <?php echo json_encode($city); ?>'), admin_url : '<?php echo $admin_url; ?>' } },
  { path: '/panel/Profile', component: Profile,
    props: {  dashboard_data: JSON.parse(`<?php echo json_encode($dashboard); ?>`)} },
  { path: '/panel/Support', component: Support,
    props: { admin_url : '<?php echo $admin_url; ?>', dashboard_data: JSON.parse(`<?php echo json_encode($dashboard); ?>`),tickets : JSON.parse('<?php echo json_encode($tickets); ?>')} },
  { path: '/panel/Charge', component: Charge,
    props: { admin_url : '<?php echo $admin_url; ?>', dashboard_data: JSON.parse(`<?php echo json_encode($dashboard); ?>`)} },
  { path: '/panel/Transactions', component: Transactions,
    props: {  dashboard_data: JSON.parse(`<?php echo json_encode($dashboard); ?>`)} },
  { path: '/panel/settings', component: Settings,
    props: {  dashboard_data: JSON.parse(`<?php echo json_encode($dashboard); ?>`)} }
];
const router = new VueRouter({
	routes,
	mode: "history",
	base: "/",
});
var app = new Vue({
	el: "#cr-dashboard",
	router,
	data() {
		return {
      dark_mode:false,
      menu:false,
      ticket_state : 'list',
      html_links : [],
      logout_popup : false,
      profile_popup : false,
      notification_popup : false,
      admin_url : '<?php echo $admin_url; ?>',
      notifications_readed : [],
      html : '',
      html_1 : '',
      html_2 : '',
      html_3 : '',
      html_4 : '',
      html_5 : '',
      plugin_path : '<?php echo plugin_dir_url( __DIR__ ); ?>',
      notification_popup_loading : false,
      // let notifications_ids : this.notifications.map(a => a.id),
      // new_notifications : notifications_ids.filter(n => !notifications_readed.includes(n));
      plugin_url : '<?php echo $plugin_url; ?>',
      currency_symbol : '<?php echo $currency_symbol; ?>' ,
      // if(currency_symbol == 'تومان'){
      //   currency_symbol_name = 'تومان'
      // }else{
      //   currency_symbol_name = 'ریال'
      // }

      dashboard_data : JSON.parse(`<?php echo json_encode($dashboard); ?>`),
      user : JSON.parse('<?php echo json_encode($current_user->data); ?>'),
      menu_items : JSON.parse('<?php echo json_encode($items); ?>'),
      dashboard_settings : JSON.parse(`<?php echo json_encode($settings); ?>`),
      all_settings : JSON.parse(`<?php echo json_encode($all_settings); ?>`),
      route : '',
      notification_content : '',
      notifications_readed : JSON.parse(`<?php echo $upanel_notifications_readed; ?>`),
      downloads : JSON.parse('<?php echo json_encode($downloads); ?>'),
      notifications : JSON.parse('<?php echo json_encode($notifications); ?>'),
      <?php if(isset($membership) && $membership == true){ ?>     
       memberships : JSON.parse('<?php echo json_encode($memberships); ?>'),
<?php } ?>

tickets : JSON.parse('<?php echo json_encode($tickets); ?>'),
data : {
  notification_popup: ''
},


	}
},
	methods: {
	ss_timer() {
	},
	},
});
</script>


