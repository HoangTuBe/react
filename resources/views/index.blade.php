<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>This is a react example</title>
  </head>
  <body>
    <div id="content">

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react-dom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.23/browser.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/0.3.2/marked.min.js"></script>

    <script type="text/babel">
      // Comment
      var Comment = React.createClass({
        rawMarkup: function() {
          var rawMarkup = marked(this.props.children.toString(), {sanitize: true});
          return {__html: rawMarkup};
        },

        render: function() {
          return (
            <div className="comment">
              <h2 className="commentAuthor">
                {this.props.author}
              </h2>
              <span dangerouslySetInnerHTML={this.rawMarkup()} />
            </div>
          );
        }
      });


      // Comment List
      var CommentList = React.createClass({
        render: function() {
          var commentNodes = this.props.data.map(function(comment) {
            return (
                <Comment author={comment.author} key={comment.id}>
                  {comment.content}
                </Comment>
            );
          });

          return (
            <div className="commentList">
              {commentNodes}
            </div>
          )
        },
      });
      // Comment form
      var CommentForm = React.createClass({
        getInitialState: function() {
          return {author: '', content: ''};
        },
        handleAuthorChange: function(e) {
          this.setState({author: e.target.value});
        },
        handleTextChange: function(e) {
          this.setState({content: e.target.value});
        },
        handleSubmit: function(e) {
          e.preventDefault();
          var author = this.state.author.trim();
          var text = this.state.content.trim();
          if (!text || !author) {
            return;
          }
          this.props.onCommentSubmit({author: author, content: text});
          this.setState({author: '', content: ''});
        },
        render: function() {
          return (
            <form className="commentForm" onSubmit={this.handleSubmit}>
              <input
                type="text"
                placeholder="Your name"
                value={this.state.author}
                onChange={this.handleAuthorChange}
              />

              <input
                type="text"
                placeholder="Say something..."
                value={this.state.content}
                onChange={this.handleTextChange}
              />

              <input type="submit" value="Post" />
            </form>
          )
        }
      });

      // This is comment box component
      var CommentBox = React.createClass({
        getInitialState: function() {
          return {data: []};
        },

        loadCommentsFromServer: function() {
          $.ajax({
            url: this.props.url,
            dataType: 'json',
            success: function(data) {
              this.setState({data: data});
            }.bind(this),
            error: function(xhr, status, err) {
              console.error(this.props.url, status, err.toString());
            }.bind(this)
          });
        },
        handleCommentSubmit: function(comment) {
          var comments = this.state.data;
          comment.id = Date.now();
          var newComments = comments.concat([comment]);
          this.setState({data: newComments});
          $.ajax({
            type: 'post',
            url: this.props.url,
            data: comment,
            success: function(data) {
              this.setState({data: data});
            }.bind(this),
            error: function(xhr, status, err) {
              this.setState({data: comments});
              console.error(this.props.url, status, err.toString());
            }.bind(this)
          });
        },
        componentDidMount: function() {
          this.loadCommentsFromServer();
          setInterval(this.loadCommentsFromServer, this.props.pollInterfval);
        },

        render: function() {
          return (
            <div className="commentBox">
              <h1>lorem ipsim</h1>
              <CommentList data={this.state.data} />
              <CommentForm onCommentSubmit={this.handleCommentSubmit} />
            </div>
          )
        },
      });


      ReactDOM.render(
        <CommentBox url="/api/comments" pollInterfval={2000} />,
        document.getElementById('content')
      );
    </script>
  </body>
</html>
