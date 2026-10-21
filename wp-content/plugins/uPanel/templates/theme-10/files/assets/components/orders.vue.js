
const Orders = {
    template: `
      <div class="orders-tab">
        <div class="row">
            <div class="col-12 col-md-12">
                <button class="back-to-orders" @click="is_single = false;">بازگشت به لیست</button>
                <div v-if="is_single" class="card-single">
   
                    <div>
                        <p><span class="label">کد سفارش</span><span class="value">         {{data_single.ID}}</span></p>
                        <p><span class="label"> مبلغ</span><span class="value"> {{formatNumber(get_price(data_single.Value_html)[0])}} <span>{{get_price(data_single.Value_html)[1]}}</span></span></p>
                        <p><span class="label"> درگاه پرداخت</span><span class="value">{{data_single.payment}} <span></span></span></p>
                    </div>
                    <div>
                        <p><span class="label"> تاریخ</span><span class="value">{{data_single.Date}}</span></p>
                        <p><span class="label"> وضعیت</span>
                        </p>
                        <span :class="'value '+ data_single.status">   
                       
                            {{data_single.status == 'completed'?'تکمیل شده':''}}
                            {{data_single.status == 'pending'?'منتظر پرداخت':''}}
                            {{data_single.status == 'processing'?'در حال پردازش':''}}
                            {{data_single.status == 'on-hold'?'در انتظار بررسی':''}}
                            {{data_single.status == 'cancelled'?'لغو شده':''}}
                            {{data_single.status == 'refunded'?'مرجوع شده':''}}
                            {{data_single.status == 'failed'?'لغو شده':''}}
                         
                    </span>

                    </div>
                    <div>
                        <button @click="" type="button">مشاهده فاکتور</button>
                    </div>
             
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <p>محصولات این سفارش</p>
                            <table>
                                <tr>
                                  <th>محصول</th>
                                  <th>تعداد</th>
                                </tr>
                                <tr v-for="product in data_single.items">
                                  <td>{{product.name}}</td>
                                  <td>{{product.quantity}}</td>
                                </tr>
                              </table>
                        </div>
                        <div class="col-12 col-md-4">
                            <p>یادداشت های سفارش</p>
                            <div class="notes">
                                <ul>
                                    <li v-for="note in data_single.notes">
                                        {{note.content}}
                                        <span class="date">{{note.date_created}}</span>
                                    </li>
                                </ul>
                     
                            </div>
                        </div>

                    </div>
                </div>
                <div v-if="!is_single" class="card orders">
                    <div v-for="(item, index) in dashboard_data['user_orders']['all']" class=" order">
                        <div>
                            <p><span class="label">کد سفارش</span><span class="value">{{item.ID}}</span></p>
                            <p><span class="label"> مبلغ</span><span class="value">     {{formatNumber(get_price(item.Value_html)[0])}} <span>{{get_price(item.Value_html)[1]}}</span></span></p>
                            <p><span class="label"> درگاه پرداخت</span><span class="value">{{item.payment}} <span></span></span></p>
                        </div>
                        <div>
                            <p><span class="label"> تاریخ</span><span class="value">{{item.Date}}</span></p>
                            <p><span class="label"> وضعیت</span><span :class="'value '+ item.status">   
                       
                                    {{item.status == 'completed'?'تکمیل شده':''}}
                                    {{item.status == 'pending'?'منتظر پرداخت':''}}
                                    {{item.status == 'processing'?'در حال پردازش':''}}
                                    {{item.status == 'on-hold'?'در انتظار بررسی':''}}
                                    {{item.status == 'cancelled'?'لغو شده':''}}
                                    {{item.status == 'refunded'?'مرجوع شده':''}}
                                    {{item.status == 'failed'?'لغو شده':''}}
                                 
                            </span></p>
                        </div>
                        <div>
                            <button @click="is_single = true;data_single = item" type="button">مشاهده جزئیات</button>
                            <button @click="data_single = item" type="button">مشاهده فاکتور</button>
                        </div>
                    </div>
                  
              </div>
            </div>
        </div>
      </div>
      `,
    data() {
      return {
        is_single:false,
        data_single:{}
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