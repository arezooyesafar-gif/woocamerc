
const Support = {
    template: `
      <div class="support-tab">

      <button v-if="ticket != -1" class="back-to-orders" @click="ticket = -1;">بازگشت به لیست</button>
      <div>
        <div style="width: 100%;margin: 0;" class="row">
            <div class="col-12 col-md-12">
        
                <div v-if="ticket != -1" class="support-single">
                    <div  class="row">
                        <div class="col-12 col-md-4">
                            <div class="card">
                                <p class="label">شماره تیکت</p>
                                <p class="value">  {{ticket.ticket_id}}</p>
                                <p class="label">عنوان</p>
                                <p class="value">  {{ticket.title}}</p>
                                <p class="label">تاریخ ثبت</p>
                                <p class="value">{{ticket.created_at}}</p>
                                <p class="label">وضعیت</p>
                                <p class="value">  
                                    <span v-if="ticket.ticket_status == 0"> در  انتظار پاسخ</span>
                                    <span v-if="ticket.ticket_status == 1"> پاسخ داده شده</span>
                                    <span v-if="ticket.ticket_status == 2"> در حال بررسی</span>
                                    <span v-if="ticket.ticket_status == 3"> بسته شده</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="card">
                                <div :class="'ticket '+ ticket.ticket_type">
                                    <div>
                                        <div v-html="ticket.ticket_text" class="text"></div>
                                        <span class="date">
                                            {{ticket.created_at}}
                                        </span>
                                    </div>
                                   
                                </div>
                                <div v-for="(item, index) in ticket.childs" :class="'ticket '+ item.ticket_type">
                                 <div>
                                    <div v-html="item.ticket_text" class="text"></div>
                                    <span class="date">
                                        {{item.created_at}}
                                    </span>
                                 </div>
                                   
                                </div>
                               
                            </div>
                            <div class="send">
                                <p class="title">ارسال پیام</p>
                                

                                <textarea v-model="replay.ticket_text" name="" id="" cols="30" rows="10"></textarea>
                                <button :disabled="replay.ticket_text.length == 0" @click="reply()" type="button">ارسال پیام</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="ticket == -1" class="card support">
                    <table>
                        <tr>
                          <th>ردیف</th>
                          <th> عنوان</th>
                          <th> تاریخ ثبت</th>
                          <th> دپارتمان</th>
                          <th>وضعیت</th>
                          <th></th>
                        </tr>
                        <tr v-for="(item, index) in tickets">
                          <td>{{item.ticket_id}}</td>
                          <td>{{item.title}}</td>
                          <td>{{item.created_at}}</td>
                          <td>{{item.department}}</td>
                          <td> 
                            <span v-if="item.ticket_status == 0"> در  انتظار پاسخ</span>
                            <span v-if="item.ticket_status == 1"> پاسخ داده شده</span>
                            <span v-if="item.ticket_status == 2"> در حال بررسی</span>
                            <span v-if="item.ticket_status == 3"> بسته شده</span>
                        </td>
                          <td><button @click="ticket = item" type="button">مشاهده</button></td>
                        </tr>
                    </table>
              </div>
            </div>
        </div>
    </div>
      </div>
      `,
    data() {
      return {
        ticket : -1,
        loading : false,
        replay:{
            ticket_id : 0,
            title : '',
            ticket_text:'',
            ticket_priority : '0',
          parent : 0,
          department : '',
          ticket_status : 0,
          user_id : '',
          ticket_type : 'user'
          }
      };
    },
    props: ['dashboard_data','tickets','admin_url'],
    computed: {
    
    },
    mounted() {

    },
  
    methods: {
        reply() {
            const _this = this
            this.loading = true
            this.replay.parent = this.ticket.id
             $.ajax({
                type: "POST",
                url: _this.admin_url,
                data: {
                  action: "hd-upanel-save-ticket",
                  data: _this.replay
                },
                success: function (results) {
                    _this.loading = false
          
                    _this.ticket.childs.push({
                    ticket_id : 0,
                    title : '',
                    ticket_text:_this.replay.ticket_text,
                    ticket_priority : '0',
                  parent : 0,
                  department : '',
                  ticket_status : 0,
                  user_id : _this.dashboard_data.user.id,
                  ticket_type : 'user'
                  })
                  _this.replay.ticket_text = ''
          
                },
                error: function (results) {
                    _this.loading = false
               
                }
              });
            }
    },
  

  };