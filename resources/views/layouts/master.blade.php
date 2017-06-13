<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') . ' - 課程管理系統' }}</title>
    </head>
    <body style="display:none"> 
            <div id="app">
                @include('layouts.navbar')
                <alert v-show="showAlert" :show="showAlert"  :type="alertSetting.type"
                  width="400px"  placement="top-right"  :duration=alertSetting.duration
                  @closed="closeAlert" > 
                       <i :class="alertSetting.class" aria-hidden="true"></i>
                            <strong v-text="alertSetting.title"></strong>
                        <p v-text="alertSetting.text"></p>
                </alert>
                <modal  :showbtn="false"   :title="editor.title" :show.sync="showUpdatedBy" 
                    effect="fade" width="800"  
                    @closed="endShowUpdatedBy">
                    <div slot="modal-body" class="modal-body">
                        <user-card v-if="showUpdatedBy" :id="editor.updated_by" ></user-card>
                    </div>
                </modal>

                <modal :showbtn="false" :width="630" :show.sync="showLogin"  effect="fade"
                    @closed="showLogin=false" >
                    <div slot="modal-header" class="modal-header">
                        <button id="close-button" type="button" class="close" data-dismiss="modal" @click="showLogin=false">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div slot="modal-body" class="modal-body">
                        <login @success="logined"></login> 
                    </div>
                </modal>
            </div>  


            <section class="section" >
                <div id="content" class="container">                
                      @yield('content')
                </div>
            </section>

            

            <script type="text/javascript" src="/js/app.js"></script>
            @include('layouts.footer')
     </body>
</html>
