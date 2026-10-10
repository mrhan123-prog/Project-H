"use strict";

const promptForm = document.getElementById("prompt-form");
const promptInput = document.getElementById("prompt");
const messages = document.getElementById("messages");

promptForm.addEventListener("submit", handlePromptSubmit);

function handlePromptSubmit(event)
{
    event.preventDefault();

    const userText = promptInput.value.trim();

    if (userText === "")
    {
        return;
    }

    console.log(userText);
}