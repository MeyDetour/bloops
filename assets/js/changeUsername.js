document.addEventListener('DOMContentLoaded', function() {
    const inputUsername = document.querySelector('#inputUsernameProfil');
    const changeButton = document.querySelector('#updateProfil');
    const saveButton = document.querySelector('#saveChanges');
    const userId = document.querySelector('#userIdProfil').value;

    console.log(inputUsername,changeButton)

    changeButton.addEventListener('click', function() {
        inputUsername.removeAttribute('readonly');
        changeButton.style.display = 'none';
        saveButton.style.display = 'block';
        inputUsername.classList.toggle('focus')
        document.querySelector('#inputUsernameProfil').style.pointerEvents = 'auto';
    });

    saveButton.addEventListener('click', function() {
        const newUsername = inputUsername.value.trim();
        if (newUsername.length > 3) {
            changeUsername(userId,newUsername).then(response=>{
                response=response.data
                console.log(response)
                inputUsername.setAttribute('readonly', true);
                saveButton.style.display = 'none';
                changeButton.style.display = 'block';
                inputUsername.classList.toggle('focus')
                document.querySelector('#inputUsernameProfil').style.pointerEvents = 'none';

            })
        } else {
            alert('Le nom d\'utilisateur doit contenir au moins 3 caractères.');
        }
    });
});

async function changeUsername(id,username){
   return await fetch(`http://localhost:8000/user/update/username/${id}/${username}`, {

    })
        .then(response => response.json())
        .then(data => {
            return data
              })
        .catch(error => {
            console.error('Error:', error);
        });
}


