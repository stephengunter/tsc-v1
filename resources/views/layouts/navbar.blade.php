<nav class="navbar navbar-default" >
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand"   style="color: #337ab7;" href="/">慈大社推課程管理系統</a>
        </div>
      
        <div id="navbar"  class="navbar-collapse collapse">
            <ul class="nav navbar-nav" style="font-weight:bold;font-size:1.2em">
                
                 @if(isset($menus))

                    @for ($i = 0; $i < count($menus); $i++)
                      <li class="{{ $menus[$i]['active'] ?  'active':'' }}">
                         <a href="{{ $menus[$i]['path'] }}" >
                            {{ $menus[$i]['text'] }}
                         </a>  
                      </li>
                    @endfor

                 @endif
            </ul>
              @if(Auth::check())
            <ul class="nav navbar-nav navbar-right">
            
              <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" 
                    role="button" aria-haspopup="true" aria-expanded="false">
                   {{  Auth::user()->name  }} <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li>
                       <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                          登出
                        </a>
                    </li>
                    <li>
                        <a href="/change-password" >變更密碼</a>
                    </li>
                  
                  
                
                  </ul>
              </li> 
            </ul>
             @endif
            <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display:none;">
                {{ csrf_field() }}
            </form>
        </div><!--/.nav-collapse -->
    
   </div><!--/.container-fluid -->
</nav>

