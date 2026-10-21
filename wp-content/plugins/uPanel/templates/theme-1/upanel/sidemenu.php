<ul  class="hd-upanel-menu" ng-class="{'sm-d-none': (route != '/' && route != '/dashboard')}">


  <li ng-repeat="n in menu_items" ng-class="(route == '/{{n}}') || 
('/' == route && n=='dashboard') || 
('/'+dashboard_settings['html_1_link'] == route && n=='html_1') || 
('/'+dashboard_settings['html_2_link'] == route && n=='html_2') || 
('/'+dashboard_settings['html_3_link'] == route && n=='html_3') || 
('/'+dashboard_settings['html_4_link'] == route && n=='html_4') || 
('/'+dashboard_settings['html_5_link'] == route && n=='html_5')?'active':''  ">


    <a ng-if="n == 'link_1' && dashboard_settings['link_1_active'] == 'on'" ng-click="menu_mobile_close()"
      href="{{dashboard_settings['link_1_link']}}"
      ng-attr-target="{{(dashboard_settings['link_1_newtab'] == 'on') ? '_blank' : undefined}}">
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
    </a>
    <a ng-if="n == 'link_2' && dashboard_settings['link_2_active'] == 'on'" ng-click="menu_mobile_close()"
      href="{{dashboard_settings['link_2_link']}}"
      ng-attr-target="{{(dashboard_settings['link_2_newtab'] == 'on') ? '_blank' : undefined}}">
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
    </a>
    <a ng-if="n == 'link_3' && dashboard_settings['link_3_active'] == 'on'" ng-click="menu_mobile_close()"
      href="{{dashboard_settings['link_3_link']}}"
      ng-attr-target="{{(dashboard_settings['link_3_newtab'] == 'on') ? '_blank' : undefined}}">
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
    </a>
    <a ng-if="n == 'link_4' && dashboard_settings['link_4_active'] == 'on'" ng-click="menu_mobile_close()"
      href="{{dashboard_settings['link_4_link']}}"
      ng-attr-target="{{(dashboard_settings['link_4_newtab'] == 'on') ? '_blank' : undefined}}">
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
    </a>
    <a ng-if="n == 'link_5' && dashboard_settings['link_5_active'] == 'on'" ng-click="menu_mobile_close()"
      href="{{dashboard_settings['link_5_link']}}"
      ng-attr-target="{{(dashboard_settings['link_5_newtab'] == 'on') ? '_blank' : undefined}}">
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
    </a>

    <a ng-if="n == 'html_1' && dashboard_settings['html_1_active'] == 'on'" ng-click="menu_mobile_close();set_html(1);"
      href="{{dashboard_settings['html_1_link']}}">
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
    </a>


    <a ng-if="n == 'html_2' && dashboard_settings['html_2_active'] == 'on'" ng-click="menu_mobile_close();set_html(2);"
      href="{{dashboard_settings['html_2_link']}}">
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
    </a>
    <a ng-if="n == 'html_3' && dashboard_settings['html_3_active'] == 'on'" ng-click="menu_mobile_close();set_html(3);"
      href="{{dashboard_settings['html_3_link']}}">
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
    </a>
    <a ng-if="n == 'html_4' && dashboard_settings['html_4_active'] == 'on'" ng-click="menu_mobile_close();set_html(4);"
      href="{{dashboard_settings['html_4_link']}}">
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
    </a>
    <a ng-if="n == 'html_5' && dashboard_settings['html_5_active'] == 'on'" ng-click="menu_mobile_close();set_html(5);"
      href="{{dashboard_settings['html_5_link']}}">
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
    </a>
    <a ng-if="n == 'dashboard' && dashboard_settings['dashboard_active'] == 'on'" class="sm-d-none"
      ng-click="menu_mobile_close();set_html(1);" href="dashboard" type="button">
      <svg width="22" height="22" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M13.977 2.744a3 3 0 00-4.098.135l-8.586 8.585 1.414 1.415L4 11.586v6.586l.005.176A3 3 0 007 21.172h10l.176-.006A3 3 0 0020 18.172v-6.586l1.293 1.293 1.414-1.415-8.586-8.585-.144-.135zM18 9.586l-5.293-5.293a1 1 0 00-1.32-.083l-.094.083L6 9.586v8.586a1 1 0 00.883.993l.117.007h10a1 1 0 00.993-.884l.007-.116V9.586zm-3 1.586h-1a1 1 0 00-1 1v1a1 1 0 001 1h1a1 1 0 001-1v-1a1 1 0 00-1-1z"
          clip-rule="evenodd"></path>
      </svg>
      {{dashboard_settings['dashboard_label']}}
    </a>

    <a ng-if="n == 'orders' && dashboard_settings['orders_active'] == 'on'" href="orders" ng-click="menu_mobile_close()"
      type="button">
      <svg width="20" height="22" viewBox="0 0 20 22" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M12 2a5 5 0 014.995 4.783L17 7h2a1 1 0 01.993.883L20 8v11a3 3 0 01-2.824 2.995L17 22H7a3 3 0 01-2.995-2.824L4 19V8a1 1 0 01.883-.993L5 7h2a5 5 0 015-5zm6 7h-1v2h-2V9H9v2H7V9H6v10a1 1 0 00.77.974l.113.02L7 20h10a1 1 0 00.993-.883L18 19V9zM9.005 6.824A3 3 0 0115 7H9l.005-.176z"
          clip-rule="evenodd"></path>
      </svg>
      {{dashboard_settings['orders_label']}}
    </a>

    <a ng-if="n == 'addresses' && dashboard_settings['address_active'] == 'on'" href="addresses"
      ng-click="menu_mobile_close()" type="button">
      <svg width="24" height="24" viewBox="0 0 24 22" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M13 1h-2v2.5H5a1 1 0 00-.928 1.371L4.923 7l-.851 2.129A1 1 0 005 10.5h6v1H6a1 1 0 00-.928.629l-1 2.5a1 1 0 000 .742l1 2.5A1 1 0 006 18.5h5v4h2v-4h6a1 1 0 00.928-1.371L19.078 15l.851-2.129A1 1 0 0019 11.5h-6v-1h5a1 1 0 00.928-.629l1-2.5a1 1 0 000-.742l-1-2.5A1 1 0 0018 3.5h-5V1zM6.928 6.629L6.477 5.5h10.846l.6 1.5-.6 1.5H6.477l.451-1.129a1 1 0 000-.742zM6.677 13.5h10.846l-.451 1.129a1 1 0 000 .742l.451 1.129H6.677l-.6-1.5.6-1.5z"
          clip-rule="evenodd"></path>
      </svg>
      {{dashboard_settings['address_label']}}
    </a>

    <a ng-if="n == 'notifications' && dashboard_settings['notifications_active'] == 'on'" ng-click="menu_mobile_close()"
      href="notifications" type="button">
      <svg width="24" height="24" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M3.667 13.886A2.001 2.001 0 005 12V9a7 7 0 0114 0v3c0 .854.54 1.606 1.333 1.886a1 1 0 01.667.943V19a1 1 0 01-1 1H4a1 1 0 01-1-1v-4.17a1 1 0 01.667-.944zM19 18v-2.535A4 4 0 0117 12V9A5 5 0 007 9v3a4 4 0 01-2 3.465V18h14zm-7 5c-1.385 0-2.563-.835-3-2h6c-.437 1.165-1.615 2-3 2z"
          clip-rule="evenodd"></path>
      </svg>
      {{dashboard_settings['notifications_label']}}
    </a>



    <a ng-if="n == 'downloads' && dashboard_settings['downloads_active'] == 'on'" href="downloads"
      ng-click="menu_mobile_close()" type="button">
      <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24">
        <path
          d="M21,14c-0.6,0-1,0.4-1,1v4c0,0.6-0.4,1-1,1H5c-0.6,0-1-0.4-1-1v-4c0-0.6-0.4-1-1-1s-1,0.4-1,1v4c0,1.7,1.3,3,3,3h14  c1.7,0,3-1.3,3-3v-4C22,14.4,21.6,14,21,14z M11.3,15.7c0.4,0.4,1,0.4,1.4,0c0,0,0,0,0,0l4-4c0.4-0.4,0.4-1,0-1.4l0,0  c-0.4-0.4-1-0.4-1.4,0c0,0,0,0,0,0L13,12.6V3c0-0.6-0.4-1-1-1s-1,0.4-1,1v9.6l-2.3-2.3c-0.4-0.4-1-0.4-1.4,0c-0.4,0.4-0.4,1,0,1.4  L11.3,15.7z" />
      </svg>
      {{dashboard_settings['downloads_label']}}
    </a>
    <a ng-if="n == 'settings' && dashboard_settings['settings_active'] == 'on'" href="settings"
      ng-click="menu_mobile_close()" type="button">
      <svg width="24" height="24" viewBox="0 0 22 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M12 2a5 5 0 015 5v1A5 5 0 017 8V7a5 5 0 015-5zm9.996 18.908C21.572 16.318 18.096 14 12 14c-6.095 0-9.572 2.318-9.996 6.908A1 1 0 003 22h18a1 1 0 00.996-1.092zM4.188 20c.728-2.677 3.231-4 7.812-4 4.58 0 7.084 1.323 7.812 4H4.188zM9 7a3 3 0 116 0v1a3 3 0 01-6 0V7z"
          clip-rule="evenodd"></path>
      </svg>

      {{dashboard_settings['settings_label']}}
    </a>


    <a ng-if="n == 'membership' && dashboard_settings['membership_active'] == 'on'" href="membership"
      ng-click="menu_mobile_close()" type="button">
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
    </a>



    <a ng-if="n == 'tickets' && dashboard_settings['tickets_active'] == 'on'" href="tickets"
      ng-click="menu_mobile_close()" type="button">


      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24"
        viewBox="0 0 22 18" style="enable-background:new 0 0 24 24;" xml:space="preserve">

        <path fill-rule="evenodd"
          d="M4.5 8.25v-1.5a3 3 0 013-3h3a3 3 0 013 3v1.5h-.75A.75.75 0 0012 9v5.25c0 .414.336.75.75.75h1.125A3.375 3.375 0 0015 8.442V6.75a4.5 4.5 0 00-4.5-4.5h-3A4.5 4.5 0 003 6.75v1.692A3.376 3.376 0 004.125 15H5.25a.75.75 0 00.75-.75V9a.75.75 0 00-.75-.75H4.5zm9.375 1.5H13.5v3.75h.375a1.875 1.875 0 000-3.75zM2.25 11.625c0-1.036.84-1.875 1.875-1.875H4.5v3.75h-.375a1.875 1.875 0 01-1.875-1.875z"
          clip-rule="evenodd"></path>
      </svg>

      {{dashboard_settings['tickets_label']}}
    </a>




    <button ng-click="logout_popup_function();menu_mobile_close()"
      ng-if="n == 'logout' && dashboard_settings['logout_active'] == 'on'" type="button">
      <svg width="24" height="24" viewBox="0 0 24 20" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
          d="M16 15h-2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v2h2V7a4 4 0 00-4-4H6a4 4 0 00-4 4v10a4 4 0 004 4h6a4 4 0 004-4v-2zm-9-2h12.586l-2.293 2.293 1.414 1.414 4-4a1 1 0 000-1.414l-4-4-1.414 1.414L19.586 11H7v2z"
          clip-rule="evenodd"></path>
      </svg>

      {{dashboard_settings['logout_label']}}
    </button>
    <div style="height:{{dashboard_settings['divider_label']}}px"
      ng-if="n == 'divider' && dashboard_settings['divider_active'] == 'on'" class="divider">

    </div>
  </li>



</ul>