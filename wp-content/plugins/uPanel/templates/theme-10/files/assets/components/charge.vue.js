
const Charge = {
    template: `
      <div class="charge-tab">
        <div style="width: 100%;margin: 0;" class="row">
          <div class="col-12 col-md-12">
            <div class="col-12 col-md-4">
              <div class="input">
                <label for="postal-code">مبلغ </label>
                <input v-model="price"  type="number" class="ltr">
              </div>
            </div>
            <button @click="charge()" type="button">شارژ حساب</button>
          </div>
        </div>

      </div>
      `,
    data() {
      return {
        loading : false,
        price:0
      };
    },
    props: ['admin_url'],
    computed: {
    
    },
    mounted() {

    },
  
    methods: {
      charge() {
            const _this = this
            this.loading = true
             $.ajax({
                type: "POST",
                url: _this.admin_url,
                data: {
                  action: "hd_upanel_charge",
                  data: _this.price
                },
                success: function (results) {
                    _this.loading = false
      
                    window.location.replace(results)
          
                },
                error: function (results) {
                    _this.loading = false
               
                }
              });
            }
    },
  

  };