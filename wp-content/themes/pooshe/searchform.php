<?php $unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>

<form role="search" method="get" class="sidebar-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <div class="input-group">
        <div class="input-group-prepend">
            <button class="btn" type="submit">جستجو</button>
        </div>
        <input type="search" class="form-control" placeholder="جستجو در وبلاگ" id="<?php echo $unique_id; ?>" value="<?php echo get_search_query(); ?>" name="s" >

    </div>
    <p class="mb-0 sub-text">جستجو در بین محتوای وبلاگ</p>
</form>