from rasa_sdk import Action
from rasa_sdk.events import SlotSet
import requests

class ActionFetchIncidents(Action):
    def name(self) -> str:
        return "action_fetch_incidents"

    def run(self, dispatcher, tracker, domain):
        incidents = self.get_incidents()
        dispatcher.utter_message(text=incidents)
        return []

    def get_incidents(self):
        try:
            response = requests.get('http://localhost/karenbot/incidents')  # Replace with your API URL
            response.raise_for_status()  # Raise an error for bad responses
            incidents_data = response.json()  # Parse the JSON response
            return self.format_incidents(incidents_data)
        except Exception as e:
            return f"Error retrieving incidents: {str(e)}"

    def format_incidents(self, data):
        messages = []
        for incident in data:  # Assuming the response is a list of incidents
            messages.append(f"Incident: {incident['name']} - Details: {incident['details']}")
        return "\n".join(messages)

class ActionFetchDemandes(Action):
    def name(self) -> str:
        return "action_fetch_demandes"

    def run(self, dispatcher, tracker, domain):
        demandes = self.get_demandes()
        dispatcher.utter_message(text=demandes)
        return []

    def get_demandes(self):
        try:
            response = requests.get('http://localhost/karenbot/demandes')  # Replace with your API URL
            response.raise_for_status()
            demandes_data = response.json()
            return self.format_demandes(demandes_data)
        except Exception as e:
            return f"Error retrieving demandes: {str(e)}"

    def format_demandes(self, data):
        messages = []
        for demande in data:  # Assuming the response is a list of demandes
            messages.append(f"Demande: {demande['name']} - Details: {demande['details']}")
        return "\n".join(messages)

class ActionFetchAppels(Action):
    def name(self) -> str:
        return "action_fetch_appels"

    def run(self, dispatcher, tracker, domain):
        appels = self.get_appels()
        dispatcher.utter_message(text=appels)
        return []

    def get_appels(self):
        try:
            response = requests.get('http://localhost/karenbot/appels')  # Replace with your API URL
            response.raise_for_status()
            appels_data = response.json()
            return self.format_appels(appels_data)
        except Exception as e:
            return f"Error retrieving appels: {str(e)}"

    def format_appels(self, data):
        messages = []
        for appel in data:  # Assuming the response is a list of appels
            messages.append(f"Appel: {appel['name']} - Details: {appel['details']}")
        return "\n".join(messages)