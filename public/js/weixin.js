(function() {
   wx.ready(function() {
         /** 微信验证成功后执行事件 */
         bindWxEvent();
       
      });

   /** 新版微信 */
   function bindWxEvent() {
      wx.onMenuShareTimeline(shareData);
      wx.onMenuShareAppMessage(shareData);
      wx.onMenuShareQQ(shareData);
      wx.onMenuShareWeibo(shareData);
   }
})();