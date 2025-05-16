#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>

// RFID Pins
#define SS_PIN      5
#define RST_PIN     22
#define SCK_PIN     18
#define MISO_PIN    19
#define MOSI_PIN    23

MFRC522 mfrc522(SS_PIN, RST_PIN);  // Create MFRC522 instance
#define ON_Board_LED 2  // On-board LED (usually GPIO2 on ESP32)

// WiFi credentials
const char* ssid     = "xample";
const char* password = "12345678";

// Server settings
const char* serverIP   = "192.168.56.105";
const uint16_t serverPort = 80;
const char* serverPath = "/rfid_attendance_gatepass/getUID.php";

// Global variables for RFID
byte readcard[4];
char str[32] = "";
String StrUID;

void setup() {
  Serial.begin(115200);
  delay(1000);

  // Initialize SPI for RFID
  SPI.begin(SCK_PIN, MISO_PIN, MOSI_PIN, SS_PIN);
  mfrc522.PCD_Init();
  Serial.println("RFID reader initialized.");

  // Setup LED
  pinMode(ON_Board_LED, OUTPUT);
  digitalWrite(ON_Board_LED, HIGH);  // LED off initially

  // Connect to WiFi
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(200);
    Serial.print(".");
    digitalWrite(ON_Board_LED, LOW);
    delay(50);
    digitalWrite(ON_Board_LED, HIGH);
    yield();  // allow background tasks
  }

  Serial.println();
  Serial.println("WiFi connected");
  Serial.print("ESP32 IP = ");
  Serial.println(WiFi.localIP());
  Serial.println("Please tag a card or keychain to see the UID!");
}

void loop() {
  // 1) Read a card UID
  if (!getid()) {
    delay(50);
    return;  // no card detected
  }

  digitalWrite(ON_Board_LED, LOW);
  Serial.println();
  Serial.print("Raw TCP test to ");
  Serial.print(serverIP);
  Serial.print(":");
  Serial.println(serverPort);

  // 2) Test raw TCP connection
  WiFiClient tcpc;
  if (!tcpc.connect(serverIP, serverPort)) {
    Serial.println(">>> TCP connect failed!");
    tcpc.stop();
    digitalWrite(ON_Board_LED, HIGH);
    delay(1000);
    return;  // skip HTTP_post
  }
  Serial.println(">>> TCP connect OK");
  tcpc.stop();

  // 3) Perform HTTP POST
  {
    HTTPClient http;
    String url = String("http://") + serverIP + serverPath;
    Serial.print("About to POST to: ");
    Serial.println(url);

    http.begin(url);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    http.setTimeout(2000);  // 2 sec timeout

    String postData = "UIDresult=" + StrUID;
    int httpCode = http.POST(postData);
    Serial.printf("HTTP code: %d (%s)\n", httpCode, http.errorToString(httpCode).c_str());

    if (httpCode > 0) {
      String payload = http.getString();
      Serial.println("Server response: " + payload);
    } else {
      Serial.println("No payload (POST failed)");
    }
    http.end();
  }

  // 4) Wrap up
  digitalWrite(ON_Board_LED, HIGH);
  delay(1000);
}

//------------------------------------------------------------------------------
// getid: returns 1 if a new card is read, 0 otherwise
int getid() {
  if (!mfrc522.PICC_IsNewCardPresent()) return 0;
  if (!mfrc522.PICC_ReadCardSerial()) return 0;

  Serial.print("RFID UID: ");
  for (int i = 0; i < mfrc522.uid.size; i++) {
    readcard[i] = mfrc522.uid.uidByte[i];
  }
  array_to_string(readcard, mfrc522.uid.size, str);
  StrUID = str;
  Serial.println(StrUID);

  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
  return 1;
}

// array_to_string: converts byte array to hex string
void array_to_string(byte array[], unsigned int len, char buffer[]) {
  for (unsigned int i = 0; i < len; i++) {
    byte nib1 = (array[i] >> 4) & 0x0F;
    byte nib2 = array[i] & 0x0F;
    buffer[i * 2]     = nib1 < 10 ? '0' + nib1 : 'A' + nib1 - 10;
    buffer[i * 2 + 1] = nib2 < 10 ? '0' + nib2 : 'A' + nib2 - 10;
  }
  buffer[len * 2] = '\0';
}
