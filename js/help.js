const chatForm =
    document.getElementById("chatForm");

const chatInput =
    document.getElementById("chatInput");

const chatMessages =
    document.getElementById("chatMessages");

const quickQuestions =
    document.getElementById("quickQuestions");

function addUserMessage(message) {

    const messageDiv =
        document.createElement("div");

    messageDiv.className =
        "message user-message";

    messageDiv.innerHTML = `

        <div class="message-content">

            <p>
                ${escapeHTML(message)}
            </p>

            <span>
                You
            </span>

        </div>

    `;

    chatMessages.appendChild(messageDiv);

    scrollChat();

}

function addBotMessage(message) {

    const messageDiv =
        document.createElement("div");

    messageDiv.className =
        "message bot-message";

    messageDiv.innerHTML = `

        <div class="message-avatar">

            <i class="fa-solid fa-sparkles"></i>

        </div>

        <div class="message-content">

            <p>
                ${message}
            </p>

            <span>
                FTDP Assistant
            </span>

        </div>

    `;

    chatMessages.appendChild(messageDiv);

    scrollChat();

}

function addLoadingMessage() {

    const loading =
        document.createElement("div");

    loading.className =
        "message bot-message";

    loading.id =
        "loadingMessage";

    loading.innerHTML = `

        <div class="message-avatar">

            <i class="fa-solid fa-sparkles"></i>

        </div>

        <div class="message-content">

            <p>
                Typing...
            </p>

        </div>

    `;

    chatMessages.appendChild(loading);

    scrollChat();

}

function removeLoadingMessage() {

    const loading =
        document.getElementById(
            "loadingMessage"
        );

    if (loading) {

        loading.remove();

    }

}

async function sendMessage(message) {

    if (!message.trim()) {

        return;

    }

    addUserMessage(message);

    chatInput.value = "";

    addLoadingMessage();

    try {

        const response =
            await fetch(
                "chatbot.php",
                {

                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/json"
                    },

                    body: JSON.stringify({
                        message: message
                    })

                }
            );

        const data =
            await response.json();

        removeLoadingMessage();

        if (data.success) {

            addBotMessage(
                escapeHTML(data.reply)
            );

        } else {

            addBotMessage(
                "Sorry, something went wrong. Please try again."
            );

        }

    } catch (error) {

        removeLoadingMessage();

        addBotMessage(
            "Unable to connect to the support service right now."
        );

        console.error(error);

    }

}

chatForm.addEventListener(
    "submit",
    function(event) {

        event.preventDefault();

        const message =
            chatInput.value.trim();

        sendMessage(message);

    }
);

quickQuestions
    .addEventListener(
        "click",
        function(event) {

            const button =
                event.target.closest("button");

            if (!button) {

                return;

            }

            const message =
                button.dataset.message;

            sendMessage(message);

        }
    );

function scrollChat() {

    chatMessages.scrollTop =
        chatMessages.scrollHeight;

}

function escapeHTML(text) {

    const div =
        document.createElement("div");

    div.textContent = text;

    return div.innerHTML;

}
