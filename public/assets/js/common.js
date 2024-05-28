// A $( document ).ready() block.
$( document ).ready(function() {

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
                
                if (status = 200 && genId) {
                    window.location.href = `/topics/${genId}`;
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