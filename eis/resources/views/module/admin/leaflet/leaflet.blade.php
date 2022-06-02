@extends('template.template3')
@section('css')
<style>
    #mapid { height: 1000px; }
 
        .pad{
            padding-top: 3rem;
        }
        @media (min-width: 992px) {
            .pad {
                padding-top: 1.8rem;
            }
        }
      
</style>
@endsection
@section('content-body')

<div class="page-wrapper pad" style="margin-top:20px" ng-controller="leafletCtrl">
    <div class="page-body">
        <div class="card">
            <div class="card-header">
                <h5>Dashboard </h5>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="panel panel-pink">
                            <div class="panel-heading bg-c-pink">
                                <span style="color:white">Sebaran Alamat By Pasien</span>
                            </div>
                            <div class="panel-body">
                                <div class="row" style="padding:10px">
                                    <div class="col-12">
                                        <div id="mapid"></div>
                                    </div>
                                    <div class="col-12">
                                        <div id="map" style="position: relative; overflow: hidden;"></div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('javascript')
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAjQfTEPdvW5C0pJwM-Iep2_DMKQXiUfGI&callback=initialize" async defer></script>-->
{{-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCK4B0WNDRex9XuXU0cTkupUIuZPQ30lj0&callback=initMap"
type="text/javascript"></script> --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDYhPXYjgCmT7ZO8jZigFm8iPXY_e16C8M&amp;libraries=places&amp;v=weekly"></script>
<script>
    // initialize()
    angular.controller('leafletCtrl', function ($scope, $http, httpService) {
        var map = L.map('mapid').setView([-1.4006881,118.5828712], 5.88);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        httpService.get('get-kota-kab-map').then(function (e) {
            for (var i = 0; i < e.data.length; i++) {
                const elem = e.data[i]
          
                L.marker([elem.lat, elem.lng]).addTo(map)
                .bindPopup("<b>" + elem.namakotakabupaten +"</b><br>"+ elem.jumlah +' Pasien')
                .openPopup();
            
            }
        })
            // let city = 'Bandung';
            // var jsonLtdLng="https://maps.googleapis.com/maps/api/geocode/json?address="+city+"&key=AIzaSyDYhPXYjgCmT7ZO8jZigFm8iPXY_e16C8M";

            // $.getJSON(jsonLtdLng, function (data) {
    
                
            //  console.log(data.results[0].geometry.location.lat);
            // });
          
           
    })
    $(document).ready(function(){
      



//    if(navigator.geolocation) {
//         navigator.geolocation.getCurrentPosition(function (position){
//             let map_lat = position.coords.latitude;
//             let map_lng = position.coords.longitude;
//             console.log(position, map_lat, map_lng);
//             initAutocomplete(map_lat, map_lng);
//         })
//     } else {
//         positionError();
//         console.log('Geolocation is not supported by this device')
//     }

//     function positionError() {    
//         console.log('Geolocation is not enabled. Please enable to use this feature')
//         if(allowGeoRecall) getLocation()
//     }

//     function toggleBounce() {
//         if (marker.getAnimation() !== null) {
//             marker.setAnimation(null);
//         } else {
//             marker.setAnimation(google.maps.Animation.BOUNCE);
//         }
//     }
//     // initAutocomplete(-6.895973,107.638)
//     function initAutocomplete(map_lat, map_lng) {
//         let myLatlng = { lat: parseFloat(map_lat) , lng: parseFloat(map_lng) };
//         let map = new google.maps.Map(document.getElementById("map"), {
//             center: myLatlng,
//             zoom: 15,
//             mapTypeId: "roadmap",
//             mapTypeControl: false,
//         });

//         marker = new google.maps.Marker({
//             map,
//             draggable: true,
//             animation: google.maps.Animation.DROP,
//             position: myLatlng,
//             title: 'Kamu Berada Disini',
//         });
//         marker.addListener("click", toggleBounce);

//         let route = "https://dokter-keluarga.mybeam.me/faskes-terdekat/latitude/longtitude";
//         route = route.replace('latitude', parseFloat(map_lat));
//         route = route.replace('longtitude', parseFloat(map_lng));
//         // console.log(route)
//         $.ajax({
//             type:'get',
//             url: route,
//             data : {'Access-Control-Allow-Origin'},
//             dataType: "json",
//             success:function(res){
//                 $.each(res.data, function (i, val) { 
//                     var marker = new google.maps.Marker({
//                         position: new google.maps.LatLng(val.map_lat,val.map_lng),
//                         map: map,
//                         title: val.name
//                     });
//                     let contentData=
//                     '<div>'+
//                         '<h5 class="font-weight-bold">'+ val.name +'</h5>'+
//                         '<div class="row justify-content-center">'+
//                             '<img class="img-fluid" src="'+ val.image +'" alt="">'+
//                         '</div>'+
//                         '<div>'+
//                             '<p>Jarak: '+ val.distances +'</p>'+
//                             '<p>Alamat: ' + val.address + '</p>'+
//                         '</div>'+
//                     '</div>';
//                     (function(marker, i) {
//                         // add click event
//                         google.maps.event.addListener(marker, 'click', function() {
//                             infowindow = new google.maps.InfoWindow({
//                                 content: contentData,
//                                 maxWidth : 400
//                             });
//                             infowindow.open(map, marker);
//                         });
//                     })(marker, i);
//                 });

//             },
//             error:function(err){
//                 // console.log(err)
//             }
//         });
//     }

    })

//     function initialize() {
//         var alamat = "PGC 1, Cililitan, Kec. Kramat jati, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta, Indonesia";
//         var geocoder = new google.maps.Geocoder;
//         geocoder.geocode({'address': alamat}, function(results, status) {
//             if (status === 'OK') {
//                 if (results[0]) {
//                     geo = results[0].geometry.location;
//                     rs = geo.lat()+", "+geo.lng();
//                 } else {
//                     rs = 'No results found';
//                 }
//             } else {
//                 rs = 'Geocoder failed due to: ' + status;
//             }
//             alert(rs);
//         });
//     }
//     var mymapDashboard;
//     $(document).ready(function(){
//         mymapDashboard = L.map('mapid').setView([-7.2540452, 112.7505882], 8);
//         setTimeout(function(){
//             L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoicmFtZGFuZWdpZSIsImEiOiJja2N1OG1uYjUyNWtjMnFsYjB3cGxiZ2RvIn0.bIO5MwJKX98q8D2-1lJ8zQ', {
//                 maxZoom: 15,
//                 attribution: 'Map data &copy; <a href="https://www.inovamedika.com/">www.inovamedika.com</a> contributors, ' +
//                     '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
//                     'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
//                 id: 'mapbox/streets-v11',
//                 tileSize: 512,
//                 zoomOffset: -1
//             }).addTo(mymapDashboard);


//             L.marker([-7.2540452, 112.7505882]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Surabaya Kota');
//             });


//             L.marker([-7.4099012, 112.6929866]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Sidoarjo ');
//             });


//             L.marker([-7.1649211, 112.6354279]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Gresik');
//             });


//             L.marker([-7.190560, 113.247195]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Sampang');
//             });


//             L.marker([-7.024420, 112.750636]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Bangkalan');
//             });


//             L.marker([-7.1548012, 113.4554739]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Pamekasan');
//             });


//             L.marker([-7.0095355, 113.8495442]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Sumenep ');
//             });


//             L.marker([-8.3474456, 113.4069761]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Jember');
//             });


//             L.marker([-7.8424207, 111.9811681]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Kediri');
//             });


//             L.marker([-7.5904682, 111.8913635]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Nganjuk ');
//             });


//             L.marker([-7.4191036, 111.4084305]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Ngawi');
//             });


//             L.marker([-8.094825, 112.1477463]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Blitar Kota');
//             });


//             // L.marker([NULL, NULL]).addTo(mymapDashboard).on('click', function(e){
//             //     setKabupaten(this, 'Gowa');
//             // });


//             L.marker([-7.6455201, 111.2994343]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Magetan');
//             });


//             // L.marker([NULL, NULL]).addTo(mymapDashboard).on('click', function(e){
//             //     setKabupaten(this, 'Ternate');
//             // });


//             L.marker([-7.5412522, 112.2008105]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Jombang');
//             });


//             L.marker([-7.4707422, 112.419833]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Mojokerto');
//             });


//             L.marker([-7.7505416, 112.6972926]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Pasuruan');
//             });


//             L.marker([-7.8716621, 111.4516344]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Ponorogo ');
//             });


//             L.marker([-6.8934518, 112.0253574]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Tuban ');
//             });


//             L.marker([-6.9052436, 107.3141495]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Bandung Barat');
//             });


//             L.marker([-7.1560884, 111.8785384]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Bojonegoro');
//             });


//             L.marker([-6.8223228, 107.1289307]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Cianjur');
//             });


//             L.marker([-7.1276047, 112.376209]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Lamongan ');
//             });


//             L.marker([-6.7040571, 110.8969791]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Pati');
//             });


//             L.marker([-7.0247246, 110.3470252]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Semarang');
//             });


//             L.marker([-7.6897123, 110.311235]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Sleman');
//             });


//             L.marker([-7.6828419, 110.761394]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Sukoharjo');
//             });


//             L.marker([-7.577444, 110.8257805]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Surakarta');
//             });


//             L.marker([-7.9786439, 112.5967635]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Malang Kota');
//             });


//             L.marker([-7.7723105, 113.1664562]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Probolinggo Kota');
//             });


//             L.marker([-7.4714887, 112.4217214]).addTo(mymapDashboard).on('click', function(e){
//                 setKabupaten(this, 'Mojokerto Kota');
//             });
//         }, 3000);



// //    mymapDashboard.on('click', onMapClick);



//     })
//     function onMapClick(e) {
//         L.popup()
//             .setLatLng(e.latlng)
//             .setContent("You clicked the map at " + e.latlng.toString())
//             .openOn(mymapDashboard);
//     }

//     function setKabupaten(obj, kabupaten){
//         // $.ajax({
//         //     type    :'POST',
//         //     url     :'https://rsdc.inovamedika.com/index.php?r=sistemInformasiEksekutif/dashboardCovid19/SetKabupaten',
//         //     data    : {kabupaten:kabupaten,'Access-Control-Allow-Origin':'*'},
//         //     dataType: "json",
//         //     success :function(data){
//                 if(kabupaten == 'Sampang'){
//                     var data ={
//                         "pasien": 3,
//                         "loadpasien": [
//                             {
//                                 "latitude": "-7.065215",
//                                 "longitude": "113.1815534",
//                                 "kecamatan_nama": "Kedungdung ",
//                                 "jumlah": "1"
//                             },
//                             {
//                                 "latitude": "-7.1530706",
//                                 "longitude": "113.1812058",
//                                 "kecamatan_nama": "Torjun",
//                                 "jumlah": "1"
//                             },
//                             {
//                                 "latitude": "-7.247838",
//                                 "longitude": "113.1956949",
//                                 "kecamatan_nama": "Sampang ",
//                                 "jumlah": "1"
//                             }
//                         ]
//                     }
//                 }
//                 if (data.pasien == undefined ){
//                     toastr.error("Kecamatan belum di set koordinatnya","Perhatian!");
//                     return false;
//                 }

//                 $.each(data.loadpasien, function(index, value){
//                     var greenIcon = new L.Icon({
//                         iconUrl: "{!! asset('js/leaflet/images/marker-icon-2x-red.png') !!}",
//                         shadowUrl: "{!! asset('js/leaflet/images/marker-shadow.png') !!}",
//                         iconSize: [25, 41],
//                         iconAnchor: [12, 41],
//                         popupAnchor: [1, -34],
//                         shadowSize: [41, 41]
//                     });

//                     L.marker([value.latitude, value.longitude],{icon: greenIcon}).addTo(mymapDashboard)
//                         .bindPopup("<b>Kecamatan "+ value.kecamatan_nama +"</b><br>"+ value.jumlah +' Pasien').openPopup();
//                 });
//                 return true;
//             // },
//             // error   : function (jqXHR, textStatus, errorThrown) { console.log(errorThrown);}
//         // });

    // }
</script>
@endsection
