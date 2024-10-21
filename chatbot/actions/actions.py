from rasa_sdk import Action
from rasa_sdk.events import SlotSet
import requests

class ActionFetchIncidents(Action):
    def name(self) -> str:
        return "action_fetch_incidents"

    def run(self, dispatcher, tracker, domain):
        search_term = tracker.latest_message.get('text')  # Get user input
        incidents = self.get_incidents(search_term)
        dispatcher.utter_message(text=incidents)
        return []

    def get_incidents(self, search_term=None):
        try:
            response = requests.get('http://localhost/karenbot/incidents')  # Replace with your API URL
            response.raise_for_status()
            incidents_data = response.json()
            return self.format_incidents(incidents_data, search_term)
        except Exception as e:
            return f"Error retrieving incidents: {str(e)}"

    def format_incidents(self, data, search_term=None):
        messages = []
        for incident in data:
            # Check if the search term is in the incident name or details
            if search_term is None or search_term.lower() in incident['name'].lower() or search_term.lower() in incident['details'].lower():
                messages.append(f"Incident: {incident['name']} - Details: {incident['details']}")
        if not messages:
            return f"No incidents found for '{search_term}'"
        return "\n".join(messages)

class ActionFetchDemandes(Action):
    def name(self) -> str:
        return "action_fetch_demandes"

    def run(self, dispatcher, tracker, domain):
        search_term = tracker.latest_message.get('text')  # Get user input
        demandes = self.get_demandes(search_term)
        dispatcher.utter_message(text=demandes)
        return []

    def get_demandes(self, search_term=None):
        try:
            response = requests.get('http://localhost/karenbot/demandes')  # Replace with your API URL
            response.raise_for_status()
            demandes_data = response.json()
            return self.format_demandes(demandes_data, search_term)
        except Exception as e:
            return f"Error retrieving demandes: {str(e)}"

    def format_demandes(self, data, search_term=None):
        messages = []
        for demande in data:
            if search_term is None or search_term.lower() in demande['name'].lower() or search_term.lower() in demande['details'].lower():
                messages.append(f"Demande: {demande['name']} - Details: {demande['details']}")
        if not messages:
            return f"No demandes found for '{search_term}'"
        return "\n".join(messages)

class ActionFetchAppels(Action):
    def name(self) -> str:
        return "action_fetch_appels"

    def run(self, dispatcher, tracker, domain):
        search_term = tracker.latest_message.get('text')  # Get user input
        appels = self.get_appels(search_term)
        dispatcher.utter_message(text=appels)
        return []

    def get_appels(self, search_term=None):
        try:
            response = requests.get('http://localhost/karenbot/appels')  # Replace with your API URL
            response.raise_for_status()
            appels_data = response.json()
            return self.format_appels(appels_data, search_term)
        except Exception as e:
            return f"Error retrieving appels: {str(e)}"

    def format_appels(self, data, search_term=None):
        messages = []
        for appel in data:
            if search_term is None or search_term.lower() in appel['name'].lower() or search_term.lower() in appel['details'].lower():
                messages.append(f"Appel: {appel['name']} - Details: {appel['details']}")
        if not messages:
            return f"No appels found for '{search_term}'"
        return "\n".join(messages)