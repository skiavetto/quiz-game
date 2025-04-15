
let perguntas = [];
let numeroPergunta = 1;  

document.getElementById("nextQuestion").addEventListener("click", function(event){
    event.preventDefault(); 

    if(numeroPergunta > 20) { 
        alert("Você já cadastrou 3 perguntas para este quiz. Este é o limite.");
        return;
    }

    let form = document.querySelector("#newQuestionForm");

    // Captura valores dos campos: 
    const pergunta = document.querySelector("#question").value.trim();
    const imgFile = document.querySelector("#imgFile").files[0];
    const altA = document.querySelector("#altA").value.trim();
    const altB = document.querySelector("#altB").value.trim();
    const altC = document.querySelector("#altC").value.trim();
    const altD = document.querySelector("#altD").value.trim();
    const rightAlt = document.querySelector("input[name='rightAlt']:checked").value;

    // Verifica se os campos foram preenchidos e cria um objeto para cada pergunta. 
    if(pergunta && altA && altB && altC && altD && rightAlt){
        const dadosDaPergunta = {
            numeroPergunta: numeroPergunta,
            pergunta: pergunta,
            imgFile: imgFile || null, 
            alternativaA: altA,
            alternativaB: altB,
            alternativaC: altC,
            alternativaD: altD,
            rightAlt: rightAlt
        }
        // Adiciona o objeto correspondente aos dados da pergunta cadastrada no array perguntas. 
        perguntas.push(dadosDaPergunta);
        form.reset(); // Limpa o formulário

        console.log("Perguntas acumuladas:", perguntas);

        numeroPergunta++;
        document.querySelector(".titleQuestion").textContent = `Pergunta ${numeroPergunta}`;
    }
    else {
        alert("Preencha todos os campos corretamente");
    }
});


// Envia as perguntas criadas pelo usuário para o arquivo saveQuiz.php 
document.getElementById("finishQuiz").addEventListener("click", function(e){
    e.preventDefault();

    if(perguntas.length === 0){
        alert("Você precisa adicionar pelo menos uma pergunta antes de finalizar o quiz !");
    }
    else {
        let formData = new FormData();

        // Percorre cada atributo de cada objeto do array perguntas e salva em formato de string em formData
        perguntas.forEach((dadosDaPergunta, index )=> {
            formData.append(`numero${index}` , dadosDaPergunta.numeroPergunta); // Confirmar se não será um problema enviar o numero como string. Apesar de que pode ser feito um parseInt caso seja necessário. 
            formData.append(`pergunta${index}`, dadosDaPergunta.pergunta);
            formData.append(`alternativaA${index}`, dadosDaPergunta.alternativaA);
            formData.append(`alternativaB${index}`, dadosDaPergunta.alternativaB);
            formData.append(`alternativaC${index}`, dadosDaPergunta.alternativaC);
            formData.append(`alternativaD${index}`, dadosDaPergunta.alternativaD);
            formData.append(`rightAlt${index}`, dadosDaPergunta.rightAlt);

            // Certifica que a imagem está sendo enviada corretamente
            if (dadosDaPergunta.imgFile) {
                formData.append(`imgFile[]`, dadosDaPergunta.imgFile); // Array de arquivos no PHP
            }

            // Verifica se existe imagem e salva em formData em formato binário (files)
            if(dadosDaPergunta.imgFile){
                formData.append(`imgFile${index}`, dadosDaPergunta.imgFile);
            }
        });

        fetch("saveQuiz.php", {
            method: "POST",
            body: formData
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url; // Redireciona se o servidor enviar um Location
            } else {
                return response.text(); // Lê a resposta como texto para evitar erro de JSON
            }
        })
        .then(data => console.log("Resposta do servidor:", data))
        .catch(error => console.error("Erro na requisição:", error));
    }
});