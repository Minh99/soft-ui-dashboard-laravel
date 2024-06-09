// A $( document ).ready() block.
$( document ).ready(function() {

    // hide toast after 5 seconds
    setTimeout(function() {
        $('.toast-push').fadeOut('slow');
    }, 3000);

    const btnGoToTopicDetail = $('.btn-go-to-topic-detail');

    btnGoToTopicDetail.on('click', function() {
        const topicId = $(this).data('topic-id');
        $('.loader').show();
        // call ajax to get topic detail
        $.ajax({
            url: `/generate-stories/${topicId}`,
            type: 'GET',
            success: function(data) {
                var data = JSON.parse(data);
                let status = data.status;
                let genId = data?.genId || null;
                let typeQuiz = data?.typeQuiz || null;
                
                if (status = 200 && genId) {
                    window.location.href = `/topics/${genId}/${typeQuiz}`;
                } else {
                    // reload page
                    alert('Something went wrong! Please try again later.');
                    window.location.reload();
                }
            },
            error: function() {
                // reload page
                alert('Something went wrong! Please try again later.');
                window.location.reload();
            },
            complete: function() {
                $('.loader').hide();
            }
        });
    });
});