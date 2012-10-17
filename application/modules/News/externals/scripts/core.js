en4.news = {

  loadComments : function(type, id, page){
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'news/viewcomments',
      data : {
        format : 'html',
        type : type,
        id : id,
        page : page
      }
    }), {
      'element' : $('comments'+id)
    });
  },
  loadContents : function(category, page, limit){	  
    en4.core.request.send(new Request.HTML({
      url : en4.core.baseUrl + 'news/loaddata',
      data : {
        format : 'html',       
        nextpage : page,
        category : category,
        limit : limit
      }
    }), {
      'element' : $('layout_news_tab_news')
    });
  }
}