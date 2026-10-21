
const Dashboard = {
    template: `
      <div class="dashboard-tab">
        <div class="row">
     
            <div class="col-12 col-md-7">
                <div class="card-welcome">
                    <img src="https://cdn.hamgardi.com/Images/Place/20210401201/400_300-1080_720/Hamgardi_4149yyvj1F8.jpg" alt="">
                    <p>علیرضا عزیز خوش آمدید.</p>
                </div>
            </div>
            <div class="col-12 col-md-5">
                <div class="card address">
                    <p class="title"><i class="icon address"></i> آدرس ها</p>
                    <div class="address">
                        <p>کرج، طالقانی شمالی، نبش بلوار مدنی، برج دورا، طبقه ۷</p>
                        <a href="">ویرایش</a>
                    </div>
                    <div class="address">
                        <p>کرج، طالقانی شمالی، نبش بلوار مدنی، برج دورا، طبقه ۷</p>
                        <a href="">ویرایش</a>
                    </div>
                    <button class="view-all" type="button">مشاهده همه آدرس ها</button>
                </div>
            </div>
            <div class="col-12">
                <div class="card orders">
                    <p class="title"><i class="icon address"></i> آخرین سفارش ها</p>
                    <table>
                        <tr>
                          <th>کد سفارش</th>
                          <th>مبلغ</th>
                          <th>درگاه پرداخت</th>
                          <th>تاریخ</th>
                          <th>وضعیت</th>
                        </tr>
                        <tr v-for="(item, index) in dashboard_data['user_orders']['all']">
                          <td>{{item.ID}}</td>
                          <td>
                          
                       
    <span>
			
      {{formatNumber(get_price(item.Value_html)[0])}}
        </span>
        <span> {{get_price(item.Value_html)[1]}} </span>
        
        </td>

                          <td>{{item.payment}}</td>
                          <td>{{item.Date}}</td>
                          <td>
                          <span :class="'status '+item.status">
                          {{item.status == 'completed'?'تکمیل شده':''}}
                          {{item.status == 'pending'?'منتظر پرداخت':''}}
                          {{item.status == 'processing'?'در حال پردازش':''}}
                          {{item.status == 'on-hold'?'در انتظار بررسی':''}}
                          {{item.status == 'cancelled'?'لغو شده':''}}
                          {{item.status == 'refunded'?'مرجوع شده':''}}
                          {{item.status == 'failed'?'لغو شده':''}}
                      </span>
                          </td>
                        </tr>
                    
                      </table>
                      <router-link  to="/panel/orders" class="view-all" @click="menu_mobile_close()" type="button"> مشاهده همه سفارش ها</router-link>

               
                </div>
            </div>
        </div>
        
      </div>
      `,
    data() {
      return {
      };
    },
    props: ['dashboard_data'],
    computed: {
    
    },
    mounted() {

    },
  
    methods: {
      get_price(html) {
        let txt = document.createElement("textarea");
      txt.innerHTML = html;
          let parser = new DOMParser();
      let doc = parser.parseFromString(txt.value, 'text/html');
      let price = doc.getElementsByTagName("bdi")[0].textContent
    
      let label = (price.includes('تومان')?'تومان':'ریال')
      return [price.replace(/\D/g,'').replace("تومان", "").replace("ريال", ""),label];
        },
      formatNumber(number) {
          let string = parseInt(number).toLocaleString('fa-IR');
          string = string.replace(/\٬/g, ",‬");
          return string;
        },
      number_fa(number) {
          let string = number.toLocaleString('fa-IR');
          string = string.replace(/\٬/g, ",‬").replaceAll(",", "");
          return string;
        }
    },
  

  };