
const Profile = {
    template: `
      <div class="prodile-tab">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card prodile">
                    <div class="input">
                        <label for="name">نام</label>
                        <input type="text" name="" id="name">
                    </div>
                    <div class="input">
                        <label for="name">نام خانوادگی</label>
                        <input type="text" name="" id="name">
                    </div>
                    
                    <button class="view-all" type="button">بروزرسانی</button>
              </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card prodile">
                    <div class="input">
                        <label for="old-pass">رمز عبور قبلی</label>
                        <input type="password" name="" id="old-pass">
                    </div>
                    <div class="input">
                        <label for="name-pass"> رمز عبور جدید</label>
                        <input type="password" name="" id="name-pass">
                    </div>
                    <div class="input">
                        <label for="repeat-new-pass">   تکراز رمز عبور جدید</label>
                        <input type="password" name="" id="repeat-new-pass">
                    </div>
                    
                    <button class="view-all" type="button">بروزرسانی</button>
              </div>
            </div>
        </div>
      </div>
      `,
    data() {
      return {
      };
    },
    computed: {
    
    },
    mounted() {

    },
  
    methods: {
  
    },
  

  };