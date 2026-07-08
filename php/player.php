<audio id="main-audio" src=""></audio>

<div class="fixed-bottom mb-4 d-flex justify-content-center" style="z-index: 1050; pointer-events: none;">
    <div class="d-flex align-items-center justify-content-between px-4 py-2" 
         style="background: rgba(6, 4, 16, 0.6); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px); 
                border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 50px; 
                width: 95%; max-width: 900px; box-shadow: 0 15px 35px rgba(0,0,0,0.5); pointer-events: auto;">
        
        <div class="d-flex align-items-center" style="width: 30%;">
            <div id="record-disk" class="rounded-circle d-flex justify-content-center align-items-center me-3" 
                 style="width: 55px; height: 55px; background: linear-gradient(135deg, #1a1a2e, #16213e); border: 2px solid rgba(255, 107, 193, 0.5);">
                <div class="rounded-circle" style="width: 12px; height: 12px; background: #060410; border: 1px solid #ff6bc1;"></div>
            </div>
            
            <div class="d-flex flex-column justify-content-center">
                <div class="d-flex align-items-end mb-1">
                    <h6 id="player-title" class="text-white mb-0 me-2 text-truncate" style="font-size: 1rem; font-weight: 700; max-width: 150px;">Оберіть трек</h6>
                    <div id="eq-bars" class="d-flex align-items-end d-none" style="height: 16px;">
                        <div class="eq-bar"></div><div class="eq-bar"></div><div class="eq-bar"></div>
                    </div>
                </div>
                <p id="player-artist" class="text-white-50 mb-0 text-truncate" style="font-size: 0.8rem; max-width: 150px;">Vestra</p>
            </div>
        </div>

        <div class="d-flex flex-column align-items-center" style="width: 40%;">
            <div class="d-flex align-items-center gap-4 mb-1 mt-1">
                <i class="bi bi-shuffle text-white-50 fs-6 player-icon d-none d-md-block" onclick="toggleActive(this)"></i>
                
                <i id="prev-btn" class="bi bi-skip-start-fill text-white fs-4 player-icon"></i>
                
                <div id="play-pause-btn" class="play-pause-btn rounded-circle d-flex justify-content-center align-items-center" 
                     style="width: 48px; height: 48px; background: linear-gradient(135deg, #d1228f, #8a43f2); cursor: pointer;">
                    <i id="play-icon" class="bi bi-play-fill text-white fs-2 ms-1"></i>
                </div>
                
                <i id="next-btn" class="bi bi-skip-end-fill text-white fs-4 player-icon"></i>
                
                <i class="bi bi-repeat text-white-50 fs-6 player-icon d-none d-md-block" onclick="toggleActive(this)"></i>
            </div>
            
            <div class="d-flex align-items-center w-100">
                <span id="current-time" class="text-white-50 me-2" style="font-size: 0.7rem;">0:00</span>
                <input type="range" id="progress-bar" class="custom-range flex-grow-1" min="0" max="100" value="0">
                <span id="total-time" class="text-white-50 ms-2" style="font-size: 0.7rem;">0:00</span>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end" style="width: 30%;">
            <i id="player-heart" class="bi bi-heart text-white-50 fs-5 player-icon me-4 d-none d-lg-block" onclick="toggleFavoritePlayer()"></i>
            <i class="bi bi-volume-up text-white-50 fs-5 player-icon me-2"></i>
            <div style="width: 80px;">
                <input type="range" id="volume-bar" class="custom-range" min="0" max="100" value="70">
            </div>
        </div>
    </div>
</div>

<script src="js/player.js"></script>
<script src="function/favorites.js"></script>