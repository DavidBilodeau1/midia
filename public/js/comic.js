document.addEventListener('DOMContentLoaded', function(){
    var correctBtn = document.querySelectorAll('.btn-correct-assoc');
    correctBtn.forEach((button, i) => {
        button.addEventListener('click', function(){
            var seriesId = this.getAttribute('data-serie-id');
            document.querySelector('#modal-1 .series-id').value = seriesId;
        });
    });
});
function addResult(match){
    var matching = document.createElement('details');
    var summary = document.createElement('summary');
    var content = document.createElement('div');
    matching.classList.add('matching-result');
    matching.classList.add('collapse-panel');
    summary.classList.add('collapse-header');
    content.classList.add('collapse-content');
    matching.setAttribute('data-result', JSON.stringify(match));
    summary.innerHTML = `<p>Nom: ${match.name}</p>`;
    content.innerHTML = `<p>Nom: ${match.name}</p>
    <p>Publié par: ${match.publisher ? match.publisher.name : 'Inconnu'}</p>
    <p>Premier numéro: ${match.first_issue ? match.first_issue.name : 'Inconnu'}</p>
    <p>Dernier numéro: ${match.last_issue ? match.last_issue.name : 'Inconnu'}</p>`;
    matching.append(summary);
    matching.append(content);
    document.querySelector('#modal-1 .collapse-group').append(matching);
}
function addResultEvent(){
    var summaries = document.querySelectorAll('summary.collapse-header');
    summaries.forEach((summary, i) => {
        summary.addEventListener('click', function(){
            if(document.querySelector('summary.collapse-header.bg-primary')){
                var oldSummary = document.querySelector('summary.collapse-header.bg-primary');
                oldSummary.classList.remove('bg-primary');
            }
            this.classList.add('bg-primary');
            document.getElementById('submit').removeAttribute('disabled');
        })
    });
}
