const Settings = {
    template: `
    <div class="settings-tab">

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card address">
                    <div class="input">
                        <label for="address">نام</label>
                        <input v-model="data.first_name" id="address" type="text">
                    </div>
                    <div class="input">
                        <label for="address">نام خانوادگی</label>
                        <input v-model="data.last_name" id="address" type="text">
                    </div>
                    <div class="input">
                        <label for="address">ایمیل</label>
                        <input v-model="data.email" id="address" type="text">
                    </div>
                    <button @click="save" class="view-all" type="button">بروزرسانی</button>
                </div>
            </div>
        </div>

    </div>
  `,
    data() {
      return {
       data:{
        first_name: '',
        last_name: '',
        email: '',
        nickname: '',
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
            var user_data = {
                first_name: this.data.first_name,
                last_name: this.data.last_name,
                email: this.data.email,
                nickname: this.data.nickname
              }
            const _this = this
            this.loading = true
             $.ajax({
                type: "POST",
                url: _this.admin_url,
                data: {
                  action: "register_update_user_info",
                  data: user_data,
                  user_id: _this.dashboard_data.user.id,
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
  
    validateAndSubmit() {
    
    },
  };






    