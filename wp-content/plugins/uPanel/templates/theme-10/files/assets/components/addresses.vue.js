
const Addresses = {
    template: `
      <div class="addresses-tab">
        <div class="row">
       
        <div class="col-12 col-md-12">
        <button v-if="addresses.length < 4" v-show="address == -1 " class="back-to-orders" @click="address = 'new';remove_marker()">آدرس جدید</button>
        <button v-show="address != -1 " class="back-to-orders" @click="address = -1;new_address={province : '',city : '',address:'',postal_code:'',lat:'',lng:''}">بازگشت به لیست</button>
        </div>

            <div class="col-12 col-md-12">
                <div v-show="address != -1 "  class="card-single">
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="input">
                                <label for="provice">استان</label>
                                <select @change="select_province" v-model="new_address.province" name="" id="provice">
                                    <option value=""></option>
                                    <option v-for="(item, index) in provinces" :value="item.id">{{item.name}}</option>
                                
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                  
                            <div class="input">
                                <label for="provice">شهر</label>
                                <select v-model="new_address.city" name="" id="provice">
                                <option value=""></option>
                                <option v-for="(item, index) in city" :value="item.id">{{item.name}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-8">
                        <div class="input">
                            <label for="address">آدرس</label>
                            <input v-model="new_address.address" id="address" type="text">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="input">
                            <label for="postal-code">کد پستی</label>
                            <input v-model="new_address.postal_code" id="postal-code" type="text">
                        </div>
                    </div>
                    <div class="col-12 col-md-12">
                        <p>موقعیت روی نقشه</p>
                        <div id="map"></div>
                        <button :disabled="!new_address.province || !new_address.city || !new_address.address || !new_address.postal_code || !new_address.lat" @click="add_address()" type="button">
                        <span v-if="address == 'new'">ثبت آدرس</span>
                        <span v-else>بروزرسانی</span>
                        </button>
                       
                    </div>
                    </div>
                </div>
              
                <div v-show="address == -1 " class="card addresses">
                    <div v-for="(item, index) in addresses" class="address">
                        <div :lat="item.lat" :lng="item.lng" class="map" :id="'map-'+item.id"></div>
                        <div class="map-info">
                            <p><span>استان:</span> {{get_province_name(item.province)}}<span>شهر:</span> {{get_city_name(item.city)}}<span>آدرس:</span> {{item.address}} </p>
                            <p><span>کد پستی:</span>  {{item.postal_code}}</p>
                            <button @click="address = item;new_address = item;update_position(item.lat,item.lng)" type="button">ویرایش</button>
                        </div>
                    </div>
                   
              </div>
            </div>
        </div>
      </div>
      `,
    data() {
      return {
        city:[],
        marker : -1,
        map : '',
        address :-1,
        new_address:{
            province : '',
            city : '',
            address:'',
            postal_code:'',
            lat:'',
            lng:''
        }
      };
    },
    props: ['addresses','admin_url','provinces','cities'],
    computed: {
    
    },
    mounted() {
        const _this = this
     
        setTimeout(function(){
         
            _this.map = L.map('map').setView([51.505, -0.09], 13);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                // attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(_this.map);
    

            _this.map.on('click', function(e) {    
                var popLocation= e.latlng; 
                if(_this.marker == -1){
                    _this.marker = L.marker(popLocation).addTo(_this.map);
                }else{
                    
                    _this.marker.setLatLng(popLocation).update();      
                }
                _this.new_address.lat = popLocation.lat
                _this.new_address.lng = popLocation.lng
            });

        }, 500);
     
   

    },
  
    methods: {
        get_province_name(id){
          return  this.provinces.filter(function (el) {
                return el.id ==  id;
              })[0]['name'];
        },
        get_city_name(id){
            var city =  this.cities.filter(function (el) {
                  return el.id ==  id;
                });
                if(city.length > 0){
                    return city[0]['name']
                }else{
                    return ''
                }
          },
        remove_marker(){
            if(this.marker != -1){
                this.map.removeLayer(this.marker)
                this.marker = -1
            }
        },
        update_position(lat,lng){
           
            const _this = this
            setTimeout(function(){
                if(_this.marker == -1){
                    _this.marker = L.marker([lat,lng]).addTo(_this.map);
                }else{
                    _this.marker.setLatLng([lat,lng]).update();      
                }
                _this.map.setView([lat,lng], 13)
                _this.city = _this.cities.filter(function (el) {
                    return el.ostan ==  _this.new_address.province;
                  });

            }, 500);

          
        },
        select_province(){
            const _this = this
            this.new_address.city = ''
           this.city = this.cities.filter(function (el) {
            return el.ostan ==  _this.new_address.province;
          });
        
        },
        add_address() {
            const _this = this
            this.loading = true
             $.ajax({
                type: "POST",
                url: _this.admin_url,
                data: {
                  action: "hd_upanel_add_address",
                  data: _this.new_address
                },
                success: function (results) {
                    _this.loading = false
                    location.reload();
                },
                error: function (results) {
                    _this.loading = false
               
                }
              });
            }
    },
  

  };