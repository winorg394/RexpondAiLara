from ollamafreeapi import OllamaFreeAPI

client = OllamaFreeAPI()

# Get instant responses
response = client.chat(
    model_name="mistral:7b-v0.2",
    prompt="Explain neural networks like I'm five",
    temperature=0.7
)
print(response)