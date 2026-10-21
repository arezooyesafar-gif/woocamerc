
const Profile = {
    template: `
      <div class="prodile-tab">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card prodile">
                    <div class="input">
                        <label for="old-pass">رمز عبور قبلی</label>
                        <input v-model="data.old_password" type="password" name="" id="old-pass">
                    </div>
                    <div class="input">
                        <label for="name-pass"> رمز عبور جدید</label>
                        <input v-model="data.old_password" type="password" name="" id="name-pass">
                    </div>
                    <div class="input">
                        <label for="repeat-new-pass">   تکراز رمز عبور جدید</label>
                        <input v-model="data.repeat_password" type="password" name="" id="repeat-new-pass">
                    </div>
                    
                    <button class="view-all" type="button">بروزرسانی</button>
              </div>
            </div>
        </div>
      </div>
      `,
    data() {
      return {
        data:{
            old_password:'',
            password:'',
            repeat_password:''
        }
      };
    },
    props: ['dashboard_data'],
    computed: {
    
    },
    mounted() {

    },
  
    methods: {
        save() {
     
            const _this = this
            this.loading = true
             $.ajax({
                type: "POST",
                url: _this.admin_url,
                data: {
                    action: "register_update_user_password",
                    password: _this.data.old_password,
                    user_id: _this.dashboard_data.user.id,
                    new_password: _this.data.password,
                },
                success: function (results) {
                    _this.loading = false
                   
                },
                error: function (results) {
                    _this.loading = false
               
                }
              });
            }
    },
  

  };